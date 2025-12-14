<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;
use Exception;

class ProfileService
{
    public function __construct(
        private UserRepository $userRepo,
    ) {
    }

    public function getCurrentUserProfile(int $userId): ?User
    {
        $user = $this->userRepo->findById($userId);

        if ($user && $user->role) {
            $user->nama_role = $user->role->nama_role;
        }

        return $user;
    }

    public function uploadKubaca(int $userId, array $file): void
    {

        $user = $this->userRepo->findById($userId);

        if (!$user) {
            throw new Exception('User tidak ditemukan');
        }

        if (!$user->isMahasiswa()) {
            throw new Exception('Hanya mahasiswa yang perlu upload KuBaca');
        }

        if (!in_array($user->status, ['pending kubaca', 'rejected'])) {
            throw new Exception('Status akun tidak memerlukan upload KuBaca');
        }

        if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File tidak valid atau gagal diupload');
        }

        $allowedTypes = ['image/png', 'image/jpeg', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Format file harus PNG, JPEG, atau WebP');
        }

        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            throw new Exception('Ukuran file maksimal 5MB');
        }
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'kubaca_' . $userId . '_' . time() . '.' . $extension;

        $uploadDir = dirname(__DIR__, 2) . '/Public/uploads/kubaca/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception('Gagal menyimpan file');
        }

        if ($user->kubaca_img) {
            $oldFile = $uploadDir . $user->kubaca_img;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $this->userRepo->update($userId, [
            'kubaca_img' => $filename,
            'status' => 'pending kubaca',
        ]);
    }
}