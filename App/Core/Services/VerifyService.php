<?php

namespace App\Core\Services;

use App\Core\Session;
use App\Models\User;
use App\Core\Services\CacheService;
use App\Core\Services\EmailService;
use App\Core\Services\Logger;

class VerifyService {
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
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