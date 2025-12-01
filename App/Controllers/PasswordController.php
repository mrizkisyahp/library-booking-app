<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Exceptions\ValidationException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Services\AuthService;
use App\Core\Services\TurnstileService;

class PasswordController extends Controller
{
    public function __construct(
        private AuthService $auth,
        private Session $session,
        private Response $response,
        private TurnstileService $turnstile
    ) {
    }

    public function forgot(Request $request)
    {
        $this->setLayout('auth');
        $this->setTitle('Forgot Password | Library Booking App');

        if ($request->isPost()) {
            $token = $request->input('cf-turnstile-response');
            $remoteIp = $request->ip();

            if (!$this->turnstile->verify($token, $remoteIp)) {
                flash('error', 'CAPTCHA Verification failed');
                return redirect('/forgot');
            }

            try {
                $validated = $request->validate([
                    'email' => ['required', 'email']
                ]);

                $success = $this->auth->sendPasswordResetOTP($validated['email']);

                if ($success) {
                    flash('success', 'Reset code sent to your email.');
                    return redirect('/reset');
                }

                flash('error', 'If that email exists, a reset code has been sent.');
                return redirect('/forgot');
            } catch (ValidationException $e) {
                return view('ResetPassword/Forgot', [
                    'validator' => $e->getValidator()
                ]);
            }
        }

        return view('ResetPassword/Forgot');
    }

    public function reset(Request $request)
    {
        $this->setLayout('auth');
        $this->setTitle('Reset Password | Library Booking App');

        $userId = session('reset_user_id');

        if (!$userId) {
            flash('error', 'Session expired, please request a new reset code.');
            return redirect('/forgot');
        }

        if ($request->isPost()) {
            $token = $request->input('cf-turnstile-response');
            $remoteIp = $request->ip();

            if (!$this->turnstile->verify($token, $remoteIp)) {
                flash('error', 'CAPTCHA verification failed');
                return redirect('/reset');
            }

            try {
                $validated = $request->validate([
                    'code' => ['required', 'string', 'min:6', 'max:6'],
                    'new_password' => ['required', 'min:8'],
                    'confirm_new_password' => ['required', 'match:new_password']
                ]);

                $success = $this->auth->resetPassword(
                    (int) $userId,
                    $validated['code'],
                    $validated['new_password']
                );

                if ($success) {
                    $this->session->remove('reset_user_id');
                    flash('success', 'Password reset successfully!');
                    return redirect('/login');
                }

                flash('error', 'Invalid reset code');
                return redirect('/reset');
            } catch (ValidationException $e) {
                return view('ResetPassword/Reset', [
                    'validator' => $e->getValidator()
                ]);
            }
        }
        return view('ResetPassword/Reset');
    }
}