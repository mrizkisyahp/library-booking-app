<?php

namespace App\Core\Services;

use App\Core\Repository\UserRepository;
use App\Core\Repository\RoleRepository;
use App\Core\Session;
use App\Core\Response;
use App\Models\User;

class AuthService
{
    private ?User $user = null;

    public function __construct(
        private Session $session,
        private UserRepository $userRepo,
        private RoleRepository $roleRepo,
        private Response $response,
        private CacheService $cache,
        private EmailService $email,
        private Logger $logger,
        private string $userClass
    ) {
    }

    public function bootstrap(): void
    {
        $userId = $this->session->get('user_id');

        if (!$userId) {
            $this->user = null;
            return;
        }

        $this->user = $this->userRepo->findById($userId);

        if (!$this->user) {
            $this->session->remove('user_id');
        }
    }

    public function attempt(array $credentials, bool $remember = false): bool
    {
        $identifier = trim($credentials['identifier'] ?? '');
        $password = $credentials['password'] ?? '';

        if (!$identifier || !$password) {
            return false;
        }

        $attemptKey = 'login_attempts_' . md5($identifier);
        $attempts = (int) ($this->cache->get($attemptKey) ?? 0);

        if ($attempts >= 5) {
            return false;
        }

        $user = $this->userRepo->findByIdentifier($identifier);

        if (!$user || !password_verify($password, $user->password)) {
            $this->cache->set($attemptKey, $attempts + 1, 900);
            return false;
        }

        if (in_array($user->status, ['suspended', 'nonaktif', 'pending verification'], true)) {
            return false;
        }

        $this->cache->delete($attemptKey);

        $this->login($user, $remember);
        return true;
    }

    public function login(User $user, bool $remember = false): void
    {
        session_regenerate_id(true);
        $this->user = $user;

        $primaryKey = $user->primaryKey();
        $id = $user->{$primaryKey};

        $this->session->set('user_id', $id);

        error_log("Login: Set user_id in session = " . $id);

        if ($remember) {
            $this->setRememberCookie($user);
        }
    }

    public function logout(): void
    {
        // $this->clearRememberCookie();
        $this->user = null;
        $this->session->remove('user_id');
    }

    public function user(): ?User
    {
        return $this->user;
    }

    public function check(): bool
    {
        return $this->user !== null;
    }

    public function guest(): bool
    {
        return $this->user === null;
    }

    public function id(): ?int
    {
        return $this->user?->id_user;
    }

    public function register(array $data, string $role): User
    {
        $roleId = $this->roleRepo->findIdByName($role);
        if (!$roleId) {
            throw new \Exception("Invalid role: {$role}");
        }

        $data['id_role'] = $roleId;
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $data['status'] = 'pending verification';

        return $this->userRepo->create($data);
    }

    public function sendVerificationOTP(User $user): string
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->cache->set("verify_otp_{$user->id_user}", password_hash($otp, PASSWORD_DEFAULT), 900);

        $this->session->remove('reset_user_id');

        $this->session->set('user_id_pending', $user->id_user);
        $this->session->remove('last_resend_time');

        $this->email->sendVerificationCode($user, $otp, 'register');

        return $otp;
    }

    public function verifyOTP(int $userId, string $code, string $prefix = 'verify_otp'): bool
    {
        $cached = $this->cache->get("{$prefix}_{$userId}");

        if (!$cached || !password_verify($code, $cached)) {
            return false;
        }

        return true;
    }

    public function completeVerification(int $userId, string $code): bool
    {
        if ($userId <= 0) {
            return false;
        }

        $attempts = (int) ($this->cache->get("verify_attempts_{$userId}") ?? 0);
        if ($attempts >= 5) {
            return false;
        }

        if (!$this->verifyOTP($userId, $code, 'verify_otp')) {
            $this->cache->set("verify_attempts_{$userId}", $attempts + 1, 900);
            return false;
        }

        $user = $this->userRepo->findById($userId);
        if (!$user) {
            $this->session->remove('user_id_pending');
            return false;
        }

        $newStatus = $user->isDosen() ? 'active' : 'pending kubaca';

        $updated = $this->userRepo->update($userId, ['status' => $newStatus]);

        if ($updated) {
            $this->cache->delete("verify_attempts_{$userId}");
            $this->cache->delete("verify_otp_{$userId}");
            $this->session->remove('user_id_pending');
            $this->logger->auth('email verified', $userId, "Status changed to: {$newStatus}");
        }

        return $updated;
    }

    public function resendVerificationOTP(int $userId): bool
    {
        $lastResend = $this->session->get('last_resend_time');
        if ($lastResend && (time() - $lastResend) < 300) {
            return false;
        }

        $user = $this->userRepo->findById($userId);
        if (!$user) {
            return false;
        }

        $this->sendVerificationOTP($user);

        $this->session->set('last_resend_time', time());

        $this->logger->auth('otp resent', $userId);

        return true;
    }

    private function setRememberCookie(User $user): void
    {
        $identifier = $user->email ?? $user->nim ?? $user->nip ?? '';

        $this->response->cookie('remember_me', $identifier, 60 * 60 * 24 * 30);
    }

    private function clearRememberCookie(): void
    {
        $this->response->cookie('remember_me', '', -1);
    }

    public function sendPasswordResetLink(string $email): bool
    {
        $user = $this->userRepo->findByEmail($email);

        if (!$user) {
            return false;
        }

        $token = bin2hex(random_bytes(32));

        $this->userRepo->update($user->id_user, [
            'password_reset_token' => hash('sha256', $token),
            'password_reset_expires' => date('Y-m-d H:i:s', time() + 900)
        ]);

        $resetLink = config('APP_URL', 'http://localhost:8000') . "/reset-password?token={$token}";
        $this->email->sendPasswordResetLink($user, $resetLink);
        $this->logger->auth('password reset link sent', $user->id_user);

        return true;
    }

    public function verifyResetToken(string $token): ?User
    {
        if (empty($token)) {
            error_log("verifyResetToken: Token is empty");
            return null;
        }

        $hashedToken = hash('sha256', $token);
        error_log("verifyResetToken: Plain token = " . $token);
        error_log("verifyResetToken: Hashed token = " . $hashedToken);
        error_log("verifyResetToken: Current time = " . date('Y-m-d H:i:s'));

        $user = User::Query()->where('password_reset_token', $hashedToken)->where('password_reset_expires', '>', date('Y-m-d H:i:s'))->with('role')->first();

        error_log("verifyResetToken: User found = " . ($user ? "ID: {$user->id_user}" : "NULL"));
        return $user;
    }

    public function resetPasswordWithToken(string $token, string $newPassword): bool
    {
        $user = $this->verifyResetToken($token);

        if (!$user) {
            return false;
        }

        $updated = $this->userRepo->update($user->id_user, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'password_reset_token' => null,
            'password_reset_expires' => null
        ]);

        if ($updated) {
            $this->logger->auth('password reset completed via link', $user->id_user);
            $this->email->sendPasswordChangedNotification($user);
        }

        return $updated;
    }
}
