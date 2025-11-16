<?php

namespace App\Core\Services;

use App\Core\Session;
use App\Models\User;
use App\Core\Services\CacheService;
use App\Core\Services\EmailService;
use App\Core\Services\Logger;

class PasswordService
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function requestReset(User $model): array
    {
        $email = trim($model->email);
        $user = User::findOne(['email' => $email]);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email not found.',
            ];
        }

        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        CacheService::set('reset_otp_' . $user->id_user, password_hash($otp, PASSWORD_DEFAULT), 900);
        EmailService::sendVerificationCode($user, $otp, 'reset_password');

        $this->session->set('reset_user_id', $user->id_user);

        Logger::auth('password reset requested', $user->id_user, 'OTP sent via email');

        return [
            'success' => true,
            'message' => 'Reset code sent to your email.',
        ];
    }

    public function resetWithOtp(User $model): array
    {
        $userId = (int)($model->id_user ?? 0);
        $user = $userId ? User::findOne(['id_user' => $userId]) : null;
        if (!$user) {
            $this->session->remove('reset_user_id');
            return [
                'success' => false,
                'message' => 'User not found. Please request a new reset code.',
            ];
        }

        $cacheKey = 'reset_otp_' . $userId;
        $cachedHash = CacheService::get($cacheKey);

        if (!$cachedHash || !password_verify(trim($model->code), $cachedHash)) {
            return [
                'success' => false,
                'message' => 'Invalid or expired code.',
            ];
        }

        $user->password = password_hash($model->new_password, PASSWORD_DEFAULT);
        $user->save();

        CacheService::delete($cacheKey);
        $this->session->remove('reset_user_id');

        Logger::auth('password reset', $userId, 'Password reset via email');

        return [
            'success' => true,
            'message' => 'Password reset successful!',
        ];
    }
}
