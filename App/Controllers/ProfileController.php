<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Request;
use App\Services\ProfileService;
use App\Core\Exceptions\ValidationException;
use App\Services\AuthService;
use App\Services\TurnstileService;

use Exception;
class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profileService,
        private AuthService $auth,
        private TurnstileService $turnstile
    ) {
    }
    public function index(Request $request)
    {
        $userId = auth()->id();

        $user = $this->profileService->getCurrentUserProfile($userId);

        return view('Profile/index', [
            'user' => $user,
        ]);
    }
    public function uploadKubaca(Request $request)
    {
        try {
            $userId = auth()->id();

            $file = $request->file('kubaca_img');

            $this->profileService->uploadKubaca($userId, $file);

            flash('success', 'KuBaca berhasil diupload. Menunggu verifikasi admin.');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
        }
        redirect('/profile');
    }

    public function detail(Request $request)
    {
        $userId = auth()->id();
        $user = $this->profileService->getCurrentUserProfile($userId);

        return view('Profile/Detail', [
            'user' => $user,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $userId = auth()->id();
        $user = $this->profileService->getCurrentUserProfile($userId);

                if ($request->isPost()) {
            $token = $request->input('cf-turnstile-response');
            $remoteIp = $request->ip();

            if (!$this->turnstile->verify($token, $remoteIp)) {
                flash('error', 'Verifikasi CAPTCHA gagal. Silakan coba lagi.');
                return redirect('/forgot');
            }

            try {
                $validated = $request->validate([
                    'email' => ['required', 'email']
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

        return view('Profile/ResetPassword', [
            'user' => $user,
        ]);
    }

    public function faq(Request $request)
    {
        return view('Profile/Faq');
    }

    public function verifikasi(Request $request)
    {
        $userId = auth()->id();
        $user = $this->profileService->getCurrentUserProfile($userId);

        return view('Profile/Verifikasi', [
            'user' => $user,
        ]);
    }
}
