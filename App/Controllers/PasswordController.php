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

                $this->auth->sendPasswordResetLink($validated['email']);

                flash('success', 'If that email exists, a password reset link has been sent. Please check your inbox.');
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

        $token = $request->query('token') ?? $request->input('token');

        if (!$token) {
            flash('error', 'Invalid or missing reset token.');
            return redirect('/forgot');
        }

        $user = $this->auth->verifyResetToken($token);

        if (!$user) {
            flash('error', 'Invalid or expired reset link. Please request a new one.');
            return redirect('/forgot');
        }

        if ($request->isPost()) {
            try {
                $validated = $request->validate([
                    'token' => ['required', 'string'],
                    'new_password' => ['required', 'min:8'],
                    'confirm_new_password' => ['required', 'match:new_password']
                ]);

                $success = $this->auth->resetPasswordWithToken(
                    $validated['token'],
                    $validated['new_password']
                );

                if ($success) {
                    flash('success', 'Password reset successful! You can now login with your new password.');
                    return redirect('/login');
                }
                flash('error', 'Failed to reset password. Please try again.');
                return redirect('/forgot');
            } catch (ValidationException $e) {
                return view('ResetPassword/Reset', [
                    'validator' => $e->getValidator(),
                    'token' => $token
                ]);
            }
        }
        return view('ResetPassword/Reset', [
            'token' => $token,
            'email' => $user->email
        ]);
    }
}