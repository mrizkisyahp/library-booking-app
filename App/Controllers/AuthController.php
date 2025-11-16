<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\App;
use App\Models\User;
use App\Core\Csrf;
use App\Core\Services\Logger;
use App\Core\Middleware\GuestMiddleware;
use App\Models\Role;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new GuestMiddleware(['logout']));
    }

    public function login(Request $request, Response $response)
    {
        $loginModel = new User();
        $loginModel->setScenario(User::SCENARIO_LOGIN);
        $authService = App::$app->auth;

        $this->setLayout('auth');
        $this->setTitle('Login | Library Booking App');

        if ($request->isPost()) {
            if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                App::$app->session->setFlash('error', 'Invalid CSRF token.');
                return $this->render('Auth/Login', ['model' => $loginModel]);
            }

            $token = $_POST['cf-turnstile-response'] ?? null;
            $remoteIp = $_SERVER['REMOTE_ADDR'] ?? null;

            if (!$authService->verifyTurnstile($token, $remoteIp)) {
                return $this->render('Auth/Login', ['model' => $loginModel]);
            }

            $loginModel->loadData($request->getBody());

            if ($loginModel->validate() && $authService->processLogin($loginModel)) {
                $currentUser = $authService->getUser();
                $roleName = null;

                if ($currentUser instanceof User && $currentUser->id_user !== null) {
                    $roleName = Role::getNameById($currentUser->id_role ?? null);
                }

                App::$app->session->setFlash('success', 'Login successful!');
                $response->redirect($roleName === 'admin' ? '/admin' : '/dashboard');
                return;
            }

            // $this->debugRegistration('login', [
            //     'request_body' => $request->getBody(),
            //     'errors' => $loginModel->getAllErrors(),
            //     'session_token' => $_SESSION['csrf_token'] ?? null,
            //     'posted_token' => $_POST['csrf_token'] ?? null,
            //     'turnstile_token' => $token ?? null,
            //     'session_flash' => $_SESSION['flash_messages'] ?? [],
            // ]);

            return $this->render('Auth/Login', ['model' => $loginModel]);
        }

        return $this->render('Auth/Login', ['model' => $loginModel]);
    }

    public function register(Request $request, Response $response)
    {
        $this->setTitle('Register | Library Booking App');
        $this->setLayout('auth');
        return $this->render('Auth/ChooseRegister');
    }

    public function registerMahasiswa(Request $request, Response $response)
    {
        $user = new User();
        $user->setScenario(User::SCENARIO_REGISTER);
        $user->id_role = Role::getIdByName('mahasiswa');
        $authService = App::$app->auth;

        $this->setTitle('Register Mahasiswa | Library Booking App');
        $this->setLayout('auth');

        if ($request->isPost()) {
            if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                App::$app->session->setFlash('error', 'Invalid CSRF token.');
                return $this->render('Auth/Mahasiswa', ['model' => $user]);
            }

            $token = $_POST['cf-turnstile-response'] ?? null;
            $remoteIp = $_SERVER['REMOTE_ADDR'] ?? null;

            if (!$authService->verifyTurnstile($token, $remoteIp)) {
                return $this->render('Auth/Mahasiswa', ['model' => $user]);
            }

            $user->loadData($request->getBody());
            $user->id_role = Role::getIdByName('mahasiswa');

            if ($user->validate()) {
                if ($authService->registerUser($user, 'mahasiswa')) {
                    App::$app->session->setFlash('success', 'Registration successful! Check your email for verification code.');
                    $response->redirect('/verify');
                    return;
                }
            }

            // $this->debugRegistration('register_mahasiswa', [
            //     'request_body'   => $request->getBody(),
            //     'errors'         => $user->getAllErrors(),
            //     'role_id'        => $user->id_role,
            //     'session_token'  => $_SESSION['csrf_token'] ?? null,
            //     'posted_token'   => $_POST['csrf_token'] ?? null,
            //     'turnstile_token'=> $token ?? null,
            //     'session_flash'  => $_SESSION['flash_messages'] ?? [],
            // ]);

            return $this->render('Auth/Mahasiswa', ['model' => $user]);
        }

        return $this->render('Auth/Mahasiswa', ['model' => $user]);
    }

    public function registerDosen(Request $request, Response $response)
    {
        $user = new User();
        $user->setScenario(User::SCENARIO_REGISTER);
        $user->id_role = Role::getIdByName('dosen');
        $authService = App::$app->auth;

        $this->setTitle('Register Dosen | Library Booking App');
        $this->setLayout('auth');

        if ($request->isPost()) {
            if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                App::$app->session->setFlash('error', 'Invalid CSRF token.');
                return $this->render('Auth/Dosen', ['model' => $user]);
            }

            $token = $_POST['cf-turnstile-response'] ?? null;
            $remoteIp = $_SERVER['REMOTE_ADDR'] ?? null;

            if (!$authService->verifyTurnstile($token, $remoteIp)) {
                return $this->render('Auth/Dosen', ['model' => $user]);
            }

            $user->loadData($request->getBody());
            $user->id_role = Role::getIdByName('dosen');
            
            if ($user->validate()) {
                if ($authService->registerUser($user, 'dosen')) {
                    App::$app->session->setFlash('success', 'Registration successful! Check your email for verification code.');
                    $response->redirect('/verify');
                    return;
                }
            }

            // $this->debugRegistration('register_dosen', [
            //     'request_body'   => $request->getBody(),
            //     'errors'         => $user->getAllErrors(),
            //     'role_id'        => $user->id_role,
            //     'session_token'  => $_SESSION['csrf_token'] ?? null,
            //     'posted_token'   => $_POST['csrf_token'] ?? null,
            //     'turnstile_token'=> $token ?? null,
            //     'session_flash'  => $_SESSION['flash_messages'] ?? [],
            // ]);

            return $this->render('Auth/Dosen', ['model' => $user]);
        }

        return $this->render('Auth/Dosen', ['model' => $user]);
    }

    public function logout(Request $request, Response $response)
    {
        $currentUser = App::$app->user;
        $userId = $currentUser instanceof User ? $currentUser->id_user : null;
        App::$app->auth->logout();
        App::$app->user = null;
        
        if ($userId) {
            Logger::auth('logged out', $userId);
        }
        
        $response->redirect('/');
    }

    // private function debugRegistration(string $label, array $context): void
    // {
    //     $enabled = filter_var($_ENV['AUTH_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);
    //     if (!$enabled) {
    //         return;
    //     }

    //     echo '<pre>';
    //     echo '[' . strtoupper($label) . ']' . PHP_EOL;
    //     print_r($context);
    //     echo '</pre>';
    // }
}
