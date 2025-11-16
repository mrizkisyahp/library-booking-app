<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\App;
use App\Models\User;
use App\Core\Csrf;
use App\Core\Services\PasswordService;

class PasswordController extends Controller
{
    public function forgot(Request $request, Response $response)
    {
        $this->setLayout('auth');
        $this->setTitle('Forgot Password | Library Booking App');

        $model = new User();
        $model->setScenario(User::SCENARIO_RESET_REQUEST);

        if ($request->isPost()) {
            if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                App::$app->session->setFlash('error', 'Invalid CSRF token.');
                return $this->render('ResetPassword/Forgot', ['model' => $model]);
            }

            $model->loadData($request->getBody());
            
            if (!$model->validate()) {
                return $this->render('ResetPassword/Forgot', ['model' => $model]);
            }

            $passwordService = new PasswordService(App::$app->session);
            $result = $passwordService->requestReset($model);

            if ($result['success']) {
                App::$app->session->setFlash('success', $result['message']);
                $response->redirect('/reset');
                return;
            }

            App::$app->session->setFlash('error', $result['message']);
            return $this->render('ResetPassword/Forgot', ['model' => $model]);
        }

        return $this->render('ResetPassword/Forgot', ['model' => $model]);
    }

    public function reset(Request $request, Response $response)
    {
        $this->setLayout('auth');
        $this->setTitle('Reset Password | Library Booking App');

        $model = new User();
        $model->setScenario(User::SCENARIO_RESET_PASSWORD);

        $userId = App::$app->session->get('reset_user_id');
        if (!$userId) {
            App::$app->session->setFlash('error', 'Session expired.');
            $response->redirect('/forgot');
            return;
        }

        if ($request->isPost()) {
            if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                App::$app->session->setFlash('error', 'Invalid CSRF token.');
                return $this->render('ResetPassword/Reset', ['model' => $model]);
            }

            $model->loadData($request->getBody());
            
            if (!$model->validate()) {
                return $this->render('ResetPassword/Reset', ['model' => $model]);
            }

            $passwordService = new PasswordService(App::$app->session);
            $model->id_user = (int)$userId;
            $result = $passwordService->resetWithOtp($model);

            if ($result['success']) {
                App::$app->session->setFlash('success', $result['message']);
                $response->redirect('/login');
                return;
            }

            App::$app->session->setFlash('error', $result['message']);
            return $this->render('ResetPassword/Reset', ['model' => $model]);
        }

        return $this->render('ResetPassword/Reset', ['model' => $model]);
    }
}
