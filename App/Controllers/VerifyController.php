<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\App;
use App\Models\User;
use App\Core\Services\VerifyService;

class VerifyController extends Controller {
    public function verify(Request $request, Response $response) {
        $this->setLayout('auth');
        $this->setTitle('Verify Account | Library Booking App');

        $userId = App::$app->session->get('user_id_pending');
        if(!$userId) {
            App::$app->session->setFlash('error', 'No pending verification. Please register again.');
            $response->redirect('/register');
            return;
        }

        $model = new User();
        $model->setScenario(User::SCENARIO_VERIFY_OTP);
        $verifyService = new VerifyService(App::$app->session);

        if ($request->isPost()) {
            $model->loadData($request->getBody());

            if (!$model->validate()) {
                return $this->render('Verify/Index', ['model' => $model]);
            }

            $model->id_user = (int)$userId;
            $result = $verifyService->verifyOtp($model);

            if (($result['success'] ?? false) === true) {
                App::$app->session->setFlash('success', $result['message'] ?? 'Account verified! You can now login.');
                $response->redirect('/login');
                return;
            }

            App::$app->session->setFlash('error', $result['message'] ?? 'Verification failed. Please try again.');
            $response->redirect('/verify');
            return;
        }

        return $this->render('Verify/Index', ['model' => $model]);
    }

    public function resend(Request $request, Response $response) {
        $userId = App::$app->session->get('user_id_pending');
        if (!$userId) {
            App::$app->session->setFlash('error', 'No Pending Verification, please register again.');
            $response->redirect('/register');
            return;
        }

        $verifyService = new VerifyService(App::$app->session);
        $result = $verifyService->resendOtp((int)$userId);

        if (($result['success'] ?? false) === true) {
            App::$app->session->setFlash('success', $result['message'] ?? 'Verification code sent to your email.');
        } else {
            App::$app->session->setFlash('error', $result['message'] ?? 'Unable to resend verification code.');
        }

        $response->redirect('/verify');
    }
}
