<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Request;
use App\Core\Response;
use App\Models\Role;
use App\Models\User;
use App\Core\Services\ProfileService;

class ProfileController extends Controller
{
    protected ?User $currentUser = null;
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['index', 'uploadKubaca']));
        $this->currentUser = App::$app->user instanceof User ? App::$app->user : null;
    }

    public function index(Request $request, Response $response)
    {
        $this->setTitle('Profile | Library Booking App');
        $this->setLayout('main');

        if (!$this->currentUser instanceof User) {
            $response->redirect('/login');
            return;
        }

        $roleName = Role::getNameById($this->currentUser->id_role ?? null);
        if ($roleName === 'mahasiswa' && $this->currentUser->status === 'pending kubaca' && !$this->currentUser->kubaca_img) {
            App::$app->session->setFlash('warning', 'Warning! Your account has not been verified fully, please upload kubaca image.');
        }

        return $this->render('Profile/Index', [
            'user' => $this->currentUser,
            'roleLabel' => $roleName,
        ]);
    }

    public function uploadKubaca(Request $request, Response $response)
    {
        if (!$request->isPost()) {
            $response->redirect('/profile');
            return;
        }

        if (!$this->currentUser instanceof User) {
            App::$app->session->setFlash('error', 'You must be logged in to upload KuBaca.');
            $response->redirect('/login');
            return;
        }

        $service = new ProfileService();
        $result = $service->uploadKubaca($this->currentUser, $_FILES['kubaca_img'] ?? null);

        $flashKey = $result['success'] ? 'success' : 'error';
        App::$app->session->setFlash($flashKey, $result['message']);

        if ($result['success']) {
            return $response->redirect('/profile');
        }

        $response->redirect('/profile');
    }
}
