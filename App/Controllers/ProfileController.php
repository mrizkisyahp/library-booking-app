<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Services\ProfileServices;
use Exception;
class ProfileController extends Controller
{
    public function __construct(
        private ProfileServices $profileServices,
    ) {
    }
    public function index(Request $request)
    {
        $userId = auth()->id();

        $user = $this->profileServices->getCurrentUserProfile($userId);

        return view('Profile/index', [
            'user' => $user,
        ]);
    }
    public function uploadKubaca(Request $request)
    {
        try {
            $userId = auth()->id();

            $file = $request->file('kubaca_img');

            $this->profileServices->uploadKubaca($userId, $file);

            flash('success', 'KuBaca berhasil diupload. Menunggu verifikasi admin.');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
        }
        redirect('/profile');
    }

    public function detail(Request $request)
    {
        $userId = auth()->id();
        $user = $this->profileServices->getCurrentUserProfile($userId);

        return view('Profile/Detail', [
            'user' => $user,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $userId = auth()->id();
        $user = $this->profileServices->getCurrentUserProfile($userId);

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
        $user = $this->profileServices->getCurrentUserProfile($userId);

        return view('Profile/Verifikasi', [
            'user' => $user,
        ]);
    }
}