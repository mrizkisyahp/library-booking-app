<?php

namespace App\Core\Services;

use App\Core\App;
use App\Models\User;
use App\Core\Services\FileUploaderService;
use App\Core\Services\Logger;

class ProfileService
{
    public function uploadKubaca(User $user, ?array $file): array
    {
        if (!$user->isMahasiswa()) {
            return ['success' => false, 'message' => 'Only mahasiswa can upload KuBaca.'];
        }

        if (!in_array($user->status, ['pending kubaca', 'rejected'], true)) {
            return ['success' => false, 'message' => 'Only pending users can upload KuBaca.'];
        }

        if ($user->kubaca_img && $user->status !== 'rejected') {
            $user->status = 'pending kubaca';
            $user->save();
            return ['success' => false, 'message' => 'You have already uploaded KuBaca image.'];
        }

        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Please select a valid image file.'];
        }

        $result = FileUploaderService::upload(
            $file,
            App::$ROOT_DIR . '/public/uploads/kubaca/',
            ['image/jpeg', 'image/png', 'image/webp'],
            2,
            'kubaca_' . $user->id_user
        );

        if (!$result['success']) {
            return ['success' => false, 'message' => $result['error'] ?? 'Failed to upload KuBaca.'];
        }
        
        $user->kubaca_img = $result['filename'];
        $user->status = 'pending kubaca';
        $user->save();

        Logger::info('KuBaca uploaded', [
            'user_id' => $user->id_user,
            'filename' => $result['filename'],
        ]);

        return ['success' => true, 'message' => 'KuBaca uploaded successfully.'];
    }
}
