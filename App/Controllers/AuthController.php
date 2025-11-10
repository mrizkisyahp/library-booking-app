<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\App;
use App\Models\User;
use App\Core\Services\EmailService;
use App\Core\Services\CacheService;
use App\Core\Services\Logger;
use App\Core\Middleware\GuestMiddleware;
use App\Models\Role;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new GuestMiddleware(['logout']));
    }

    private function verifyTurnstile(Response $response): bool
    {
        $enabled = filter_var($_ENV['TURNSTILE_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN);
        if (!$enabled) {
            return true;
        }

        $token  = $_POST['cf-turnstile-response'] ?? null;
        $secret = $_ENV['TURNSTILE_SECRET'] ?? null;

        if (!$token || !$secret) {
            App::$app->session->setFlash('error', 'CAPTCHA token is missing. Please try again.');
            return false;
        }

        $payload = http_build_query([
            'secret'   => $secret,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $payload,
            ],
        ]);

        $verify = @file_get_contents('https://challenges.cloudflare.com/turnstile/v0/siteverify', false, $context);

        if ($verify === false) {
            App::$app->session->setFlash('error', 'Could not connect to Turnstile API.');
            return false;
        }

        $result = json_decode($verify, true);
        return isset($result['success']) && $result['success'] === true;
    }

    public function login(Request $request, Response $response)
    {
        $loginModel = new User();
        $loginModel->setScenario(User::SCENARIO_LOGIN);

        $this->setLayout('auth');
        $this->setTitle('Login | Library Booking App');

        if ($request->isPost()) {
            if (!\App\Core\Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                App::$app->session->setFlash('error', 'Invalid CSRF token.');
                return $this->render('Auth/Login', ['model' => $loginModel]);
            }
        
        if (!$this->verifyTurnstile($response)) {
            App::$app->session->setFlash('error', 'Turnstile verification gagal');
            return $this->render('Auth/Login', ['model' => $loginModel]);
        }

            $loginModel->loadData($request->getBody());

            if ($loginModel->validate() && $loginModel->login()) {
                $currentUser = App::$app->user;
                $roleName = null;

                if ($currentUser instanceof User && $currentUser->id_user !== null) {
                    Logger::auth('logged in', $currentUser->id_user);
                    $roleName = Role::getNameById($currentUser->id_role ?? null);
                }

                App::$app->session->setFlash('success', 'Login successful!');
                $response->redirect($roleName === 'admin' ? '/admin' : '/dashboard');
                return;
            }

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

        $this->setTitle('Register Mahasiswa | Library Booking App');
        $this->setLayout('auth');

        if ($request->isPost()) {
            if (!\App\Core\Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                App::$app->session->setFlash('error', 'Invalid CSRF token.');
                return $this->render('Auth/Mahasiswa', ['model' => $user]);
            }

        if (!$this->verifyTurnstile($response)) {
            App::$app->session->setFlash('error', 'Turnstile verification failed.');
            return $this->render('Auth/Mahasiswa', ['model' => $user]);
            }

            $user->loadData($request->getBody());
            $user->id_role = Role::getIdByName('mahasiswa');

            if ($user->validate() && $user->save()) {
                $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                CacheService::set('otp_' . $user->id_user, password_hash($otp, PASSWORD_DEFAULT), 900);
                
                App::$app->session->set('user_id_pending', $user->id_user);
                EmailService::sendVerificationCode($user, $otp, 'register');

                Logger::auth('registered', $user->id_user, "Email: {$user->email}, Role: mahasiswa");
                App::$app->session->setFlash('success', 'Registration successful! Check your email for verification code.');
                $response->redirect('/verify');
                return;
            }

            return $this->render('Auth/Mahasiswa', ['model' => $user]);
        }

        return $this->render('Auth/Mahasiswa', ['model' => $user]);
    }

    public function registerDosen(Request $request, Response $response)
    {
        $user = new User();
        $user->setScenario(User::SCENARIO_REGISTER);
        $user->id_role = Role::getIdByName('dosen');

        $this->setTitle('Register Dosen | Library Booking App');
        $this->setLayout('auth');

        if ($request->isPost()) {
            if (!\App\Core\Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                App::$app->session->setFlash('error', 'Invalid CSRF token.');
                return $this->render('Auth/Dosen', ['model' => $user]);
            }

            $user->loadData($request->getBody());
            $user->id_role = Role::getIdByName('dosen');

            if ($user->validate() && $user->save()) {
                $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                CacheService::set('otp_' . $user->id_user, password_hash($otp, PASSWORD_DEFAULT), 900);
                
                App::$app->session->set('user_id_pending', $user->id_user);
                EmailService::sendVerificationCode($user, $otp, 'register');

                Logger::auth('registered', $user->id_user, "Email: {$user->email}, Role: dosen");
                App::$app->session->setFlash('success', 'Registration successful! Check your email for verification code.');
                $response->redirect('/verify');
                return;
            }

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

}
