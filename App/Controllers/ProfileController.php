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
use App\Core\Services\FileUploaderService;

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

        $roleName = Role::getNameById($user->id_role ?? null);
        if ($roleName === 'mahasiswa' && $user->status === 'pending kubaca' && !$user->kubaca_img) {
            App::$app->session->setFlash('warning', 'Warning! Your account has not been verified fully, please upload kubaca image.');
        }

        return $this->render('Profile/Index', [
            'user' => $user,
            'roleLabel' => $roleName,
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
        
        if ($user->status !== 'pending kubaca' && $user->status !== 'rejected') {
            App::$app->session->setFlash('error', 'Only pending users can upload KuBaca.');
            $response->redirect('/profile');
            return;
        }

        if ($user->kubaca_img && $user->status !== 'rejected') {
            App::$app->session->setFlash('error', 'You have already uploaded KuBaca image.');
            $user->status = 'pending kubaca';
            $user->save();
            $response->redirect('/profile');
            return;
        }

        if (!isset($_FILES['kubaca_img']) || $_FILES['kubaca_img']['error'] !== UPLOAD_ERR_OK) {
            App::$app->session->setFlash('error', 'Please select a valid image file.');
            $response->redirect('/profile');
            return;
        }

        $result = FileUploaderService::upload(
        $_FILES['kubaca_img'],
        App::$ROOT_DIR . '/public/uploads/kubaca/',
        ['image/jpeg', 'image/png', 'image/webp'],
        2,
        'kubaca_' . $user->id_user
        );

        if (!$result['success']) {
            App::$app->session->setFlash('error', $result['error']);
            return $response->redirect('/profile');
        }

        $stmt = App::$app->db->prepare("
            UPDATE users SET kubaca_img = :img WHERE id_user = :id
        ");

        $stmt->bindValue(':img', $result['filename']);
        $stmt->bindValue(':id', $user->id_user, \PDO::PARAM_INT);
        $stmt->execute();

        $user->kubaca_img = $result['filename'];
        $user->status = 'pending kubaca';
        $user->save();

        if (class_exists(Logger::class)) {
            Logger::info('KuBaca uploaded', [
                'user_id' => $user->id_user,
                'filename' => $result['filename']
            ]);
        }

        App::$app->session->setFlash('success', 'KuBaca uploaded successfully.');
        return $response->redirect('/profile');
    }
}
