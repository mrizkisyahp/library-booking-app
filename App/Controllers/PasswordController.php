<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Exceptions\ValidationException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;
use App\Services\TurnstileService;

class PasswordController extends Controller
{
    public function __construct(
        private AuthService $auth,
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
                flash('error', 'Verifikasi CAPTCHA gagal. Silakan coba lagi.');
                return redirect('/forgot');
            }

            try {
                $validated = $request->validate([
                    'email' => 'required|email'
                ]);

                $sent = $this->auth->sendPasswordResetLink($validated['email']);

                if (!$sent) {
                    flash('info', 'Jika email tersebut terdaftar, password reset link tidak dapat dikirim. Silakan coba lagi.');
                } else {
                    flash('success', 'Jika email tersebut terdaftar, password reset link telah dikirim.');
                }

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
            flash('error', 'Tautan reset password tidak valid. Silakan coba lagi.');
            return redirect('/forgot');
        }

        $user = $this->auth->verifyResetToken($token);

        if (!$user) {
            flash('error', 'Tautan reset password tidak valid. Silakan coba lagi.');
            return redirect('/forgot');
        }

        if ($request->isPost()) {
            try {
                $validated = $request->validate([
                    'token' => 'required|string',
                    'new_password' => 'required|min:8',
                    'confirm_new_password' => 'required|match:new_password'
                ]);

                $success = $this->auth->resetPasswordWithToken(
                    $validated['token'],
                    $validated['new_password']
                );

                if ($success) {
                    auth()->logout();
                    flash('success', 'Pengaturan password berhasil diubah. Silakan login kembali.');
                    return redirect('/login');
                }
                flash('error', 'Gagal mengubah pengaturan password. Silakan coba lagi.');
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
