<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Services\Logger;
use App\Models\Role;
use App\Models\User;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['index', 'uploadKubaca']));
    }

    public function index(Request $request, Response $response)
    {
        $this->setTitle('Profile | Library Booking App');
        $this->setLayout('main');

        $user = App::$app->user;
        if (!$user instanceof User) {
            $response->redirect('/login');
            return;
        }

        $roleLabel = Role::getNameById($user->id_role ?? null) ?? 'Unknown';

        return $this->render('Profile/Index', [
            'user' => $user,
            'roleLabel' => $roleLabel,
        ]);
    }

    public function uploadKubaca(Request $request, Response $response)
    {
        if (!$request->isPost()) {
            $response->redirect('/profile');
            return;
        }

        $user = App::$app->user;
        if (!$user instanceof User) {
            App::$app->session->setFlash('error', 'You must be logged in to upload KuBaca.');
            $response->redirect('/login');
            return;
        }

        if (!$user->isMahasiswa()) {
            App::$app->session->setFlash('error', 'Only mahasiswa can upload KuBaca.');
            $response->redirect('/profile');
            return;
        }
        
        if ($user->status !== 'verified') {
            App::$app->session->setFlash('error', 'Only active users can upload KuBaca.');
            $response->redirect('/profile');
            return;
        }

        if ($user->kubaca_img) {
            App::$app->session->setFlash('error', 'You have already uploaded KuBaca image.');
            $response->redirect('/profile');
            return;
        }

        if (!isset($_FILES['kubaca_img']) || $_FILES['kubaca_img']['error'] !== UPLOAD_ERR_OK) {
            App::$app->session->setFlash('error', 'Please select a valid image file.');
            $response->redirect('/profile');
            return;
        }

        $file = $_FILES['kubaca_img'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            App::$app->session->setFlash('error', 'Only JPG, PNG, and WEBP images are allowed.');
            $response->redirect('/profile');
            return;
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            App::$app->session->setFlash('error', 'File size must be less than 2MB.');
            $response->redirect('/profile');
            return;
        }

        $uploadDir = App::$ROOT_DIR . '/public/uploads/kubaca/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'kubaca_' . $user->id_user . '_' . time() . '.' . $extension;
        $uploadPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $stmt = App::$app->db->prepare("UPDATE users SET kubaca_img = :img WHERE id_user = :id");
            $stmt->bindValue(':img', $filename);
            $stmt->bindValue(':id', $user->id_user, \PDO::PARAM_INT);
            $stmt->execute();

            $user->kubaca_img = $filename;
            Logger::info('KuBaca image uploaded', [
                'user_id' => $user->id_user,
                'filename' => $filename
            ]);
            App::$app->session->setFlash('success', 'KuBaca image uploaded successfully.');

            $response->redirect('/profile');
        } else {
            Logger::error('Failed to upload KuBaca image', [
                'user_id' => $user->id_user
            ]);
            App::$app->session->setFlash('error', 'Failed to upload image. Please try again.');
            $response->redirect('/profile');
        }
    }
}
