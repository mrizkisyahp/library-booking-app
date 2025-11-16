<?php

namespace App\Core\Services;

use App\Core\App;
use App\Core\DbModel;
use App\Core\Response;
use App\Core\Session;
use App\Models\User;
use App\Core\Services\CacheService;
use App\Core\Services\EmailService;
use App\Core\Services\Logger;

class AuthService
{
    private Session $session;
    private string $userClass;
    private ?DbModel $user = null;

    public function __construct(Session $session, string $userClass)
    {
        $this->session = $session;
        $this->userClass = $userClass;
    }

    public function bootstrap(): void
    {
        $primaryValue = $this->session->get('user');

        if (!$primaryValue) {
            $this->user = null;
            return;
        }

        $primaryKey = $this->userClass::primaryKey();
        $user = $this->userClass::findOne([$primaryKey => $primaryValue]);

        if ($user instanceof DbModel) {
            $this->user = $user;
            return;
        }

        $this->session->remove('user');
        $this->user = null;
    }

    public function login(DbModel $user): void
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};

        $this->session->set('user', $primaryValue);
    }

    public function logout(): void
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public function getUser(): ?DbModel
    {
        return $this->user;
    }

    public function isGuest(): bool
    {
        return $this->user === null;
    }

    public function verifyTurnstile(?string $token, ?string $remoteIp): bool
    {
        $enabled = filter_var($_ENV['TURNSTILE_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if (!$enabled) {
            return true;
        }

        $secret = $_ENV['TURNSTILE_SECRET'] ?? null;
        if (!$token || !$secret) {
            $this->session->setFlash('error', 'CAPTCHA token is missing. Please try again.');
            return false;
        }

        $payload = http_build_query([
            'secret'   => $secret,
            'response' => $token,
            'remoteip' => $remoteIp,
        ]);

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $payload,
            ],
        ]);

        $verify = @file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $context);

        if ($verify === false) {
            $this->session->setFlash('error', 'Could not connect to Turnstile API.');
            return false;
        }

        $result = json_decode($verify, true);
        if (($result['success'] ?? false) === true) {
            return true;
        }

        $this->session->setFlash('error', 'Turnstile verification failed.');
        return false;
    }

    public function processLogin(User $loginModel): bool
    {
        $identifier = trim($loginModel->identifier);
        $user = User::findOne(['email' => $identifier]);

        if (!$user) {
            $user = User::findOne(['nim' => $identifier]);
        }

        if (!$user) {
            $user = User::findOne(['nip' => $identifier]);
        }

        if (!$user) {
            $loginModel->addError('identifier', 'User not found');
            return false;
        }

        if (!password_verify($loginModel->password, $user->password)) {
            $loginModel->addError('password', 'Password is incorrect');
            return false;
        }

        if ($user->status === 'suspended') {
            $loginModel->addError('identifier', 'Your account has been suspended. Please contact support.');
            return false;
        }

        if ($user->status === 'pending verification') {
            $loginModel->addError('identifier', 'Your account is pending verification. Please check your email.');
            return false;
        }

        $this->login($user);
        App::$app->user = $this->getUser();
        if ($user->id_user !== null) {
            Logger::auth('logged in', $user->id_user);
        }

        return true;
    }

    public function registerUser(User $user, string $roleName): bool
    {
        $status = $roleName === 'dosen' ? 'pending kubaca' : 'pending verification';
        $user->status = $status;
        $user->password = password_hash($user->password, PASSWORD_DEFAULT);

        if (!$user->save()) {
            return false;
        }

        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        CacheService::set('otp_' . $user->id_user, password_hash($otp, PASSWORD_DEFAULT), 900);

        $this->session->set('user_id_pending', $user->id_user);
        $this->session->remove('last_resend_time');

        EmailService::sendVerificationCode($user, $otp, 'register');

        if ($user->id_user !== null) {
            $details = "Email: {$user->email}, Role: {$roleName}";
            Logger::auth('registered', $user->id_user, $details);
        }

        return true;
    }

    public function verifyOtp(User $model): array
    {
        $userId = (int)($model->id_user ?? 0);
        if ($userId <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid verification context.',
            ];
        }

        $cacheKey = 'otp_' . $userId;
        $cachedHash = CacheService::get($cacheKey);

        if (!$cachedHash) {
            return [
                'success' => false,
                'message' => 'Verification code expired. Please try again.',
            ];
        }

        if (!password_verify(trim($model->code), $cachedHash)) {
            return [
                'success' => false,
                'message' => 'Invalid verification code. Please try again.',
            ];
        }

        $user = User::findOne(['id_user' => $userId]);
        if (!$user) {
            CacheService::delete($cacheKey);
            $this->session->remove('user_id_pending');

            return [
                'success' => false,
                'message' => 'User not found. Please register again.',
            ];
        }

        $newStatus = $user->isDosen() ? 'active' : 'pending kubaca';
        $user->status = $newStatus;
        $user->save();

        CacheService::delete($cacheKey);
        $this->session->remove('user_id_pending');

        Logger::auth('email verified', $userId, "Status changed to: {$newStatus}");

        $successMessage = $user->isDosen()
            ? 'Account verified! You can now login.'
            : 'Email verified! You can now login.';

        return [
            'success' => true,
            'message' => $successMessage,
        ];
    }

    public function resendOtp(int $userId): array
    {
        $user = User::findOne(['id_user' => $userId]);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'User not found. Please register again.',
            ];
        }

        $lastResend = $this->session->get('last_resend_time');
        if ($lastResend && time() - $lastResend < 60) {
            return [
                'success' => false,
                'message' => 'Please wait 1 minute before resending.',
            ];
        }

        $this->session->set('last_resend_time', time());

        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        CacheService::set('otp_' . $userId, password_hash($otp, PASSWORD_DEFAULT), 900);
        EmailService::sendVerificationCode($user, $otp, 'register');

        Logger::auth('otp resent', $userId);

        return [
            'success' => true,
            'message' => 'Verification code sent to your email.',
        ];
    }

}
