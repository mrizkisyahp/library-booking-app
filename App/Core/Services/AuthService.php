<?php

namespace App\Core\Services;

use App\Core\Repository\UserRepository;
use App\Core\Session;
use App\Core\Response;
use App\Models\User;

class AuthService
{
    private ?User $user = null;

    public function __construct(
        private Session $session,
        private UserRepository $userRepo,
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

        $user = $this->userRepo->findByIdentifier($identifier);

        if (!$user || !password_verify($password, $user->password)) {
            return false;
        }

        if (in_array($user->status, ['suspended', 'nonaktif', 'pending verification'], true)) {
            return false;
        }

        $this->login($user, $remember);
        return true;
    }

    public function login(User $user, bool $remember = false): void
    {
        $this->user = $user;

        $primaryKey = $user->primaryKey();
        $id = $user->{$primaryKey};

        $this->session->set('user_id', $id);

        error_log("Login: Set user_id in session = " . $id);

        if ($remember) {
            $this->setRememberCookie($user->email ?? $user->nim ?? $user->nip ?? '');
        }
    }

    public function logout(): void
    {
        $this->user = null;
        $this->session->remove('user_id');
        $this->clearRememberCookie();
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
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $data['status'] = $role === 'dosen'
            ? 'active'
            : 'pending verification';

        return $this->userRepo->create($data);
    }

    public function sendVerificationOTP(User $user): string
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->cache->set("verify_otp_{$user->id_user}", password_hash($otp, PASSWORD_DEFAULT), 900);

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

        $this->cache->delete("{$prefix}_{$userId}");
        return true;
    }

    public function completeVerification(int $userId, string $code): bool
    {
        if ($userId <= 0) {
            return false;
        }

        if (!$this->verifyOTP($userId, $code, 'verify_otp')) {
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

    public function sendPasswordResetOTP(string $email): bool
    {
        $user = $this->userRepo->findByEmail($email);

        if (!$user) {
            return false;
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->cache->set("reset_otp_{$user->id_user}", password_hash($otp, PASSWORD_DEFAULT), 900);

        $this->session->set('reset_user_id', $user->id_user);

        $this->email->sendVerificationCode($user, $otp, 'reset');

        $this->logger->auth('password reset otp sent', $user->id_user);

        return true;
    }

    public function resetPassword(int $userId, string $code, string $password): bool
    {
        if (!$this->verifyOTP($userId, $code, 'reset_otp')) {
            return false;
        }

        return $this->userRepo->update($userId, [
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    private function setRememberCookie(string $identifier): void
    {
        $this->response->cookie('remember_identifier', $identifier, 60 * 60 * 24 * 30);
    }

    private function clearRememberCookie(): void
    {
        $this->response->cookie('remember_identifier', '', -1);
    }
}
