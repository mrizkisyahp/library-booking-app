<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Exceptions\ValidationException;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;
use App\Services\TurnstileService;

class VerifyController extends Controller
{
    public function __construct(
        private AuthService $auth,
        private TurnstileService $turnstile
    ) {
    }

    public function verify(Request $request)
    {
        $this->setLayout('auth');
        $this->setTitle('Verify Account | Library Booking App');

        $userId = session('user_id_pending');

        if (!$userId) {
            flash('error', 'No pending verification. Please register again.');
            return redirect('/register');
        }

        if ($request->isPost()) {

            $token = $request->input('cf-turnstile-response');
            $remoteIp = $request->ip();

            if (!$this->turnstile->verify($token, $remoteIp)) {
                flash('error', 'CAPTCHA verification failed.');
                return redirect('/verify');
            }

            try {
                $validated = $request->validate([
                    'code' => 'required|string|min:6|max:6'
                ]);

                $success = $this->auth->completeVerification((int) $userId, $validated['code']);

                if ($success) {
                    flash('success', 'Email verified! You can now login.');
                    return redirect('/login');
                }

            } catch (ValidationException $e) {
                return view('Verify/index', [
                    'validator' => $e->getValidator()
                ]);
            }

            flash('error', 'invalid or expired verification code. Please try again.');
            return redirect('/verify');
        }

        return view('Verify/index');
    }

    public function resend(Request $request)
    {
        $userId = session('user_id_pending');

        if (!$userId) {
            flash('error', 'No pending verification. Please register again.');
            return redirect('/register');
        }

        $success = $this->auth->resendVerificationOTP((int) $userId);

        if ($success) {
            flash('success', 'Verification code sent to your email.');
            return redirect('/verify');
        }

        flash('error', 'Please wait 5 minutes before requesting another code.');
        return redirect('/verify');
    }
}
