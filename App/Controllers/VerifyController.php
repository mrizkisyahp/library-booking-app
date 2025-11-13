<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\App;
use App\Models\User;
use App\Models\VerificationForm;
use App\Core\Services\EmailService;
use App\Core\Services\CacheService;
use App\Core\Services\Logger;

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

        $model = new VerificationForm();

        if ($request->isPost()) {
            $model->loadData($request->getBody());

            if (!$model->validate()) {
                return $this->render('Verify/Index', ['model' => $model]);
            }

            $code = trim($model->code);
            $cachedHash = CacheService::get('otp_' . $userId);

            if (!$cachedHash) {
                App::$app->session->setFlash('error', 'Verification code expired. Please try again.');
                $response->redirect('/verify');
                return;
            }

            if (!password_verify($code, $cachedHash)) {
                App::$app->session->setFlash('error', 'Invalid verification code. Please try again.');
                $response->redirect('/verify');
                return;
            }

            $user = User::findOne($userId);
            $newStatus = ($user->isDosen()) ? 'active' : 'pending kubaca';

            $user->status = $newStatus;
            $user->save();
            
            CacheService::delete('otp_' . $userId);
            App::$app->session->remove('user_id_pending');

            Logger::auth('email verified', $userId, "Status changed to: {$newStatus}");
            
            if ($user->isDosen()) {
                App::$app->session->setFlash('success', 'Account verified! You can now login.');
            } else {
                App::$app->session->setFlash('success', 'Email verified! You can now login.');
            }

            $response->redirect('/login');
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

        $user = User::findOne(['id_user' => $userId]);
        if (!$user) {
            App::$app->session->setFlash('error', 'User not found. Please register again.');
            $response->redirect('/register');
            return;
        }

        $lastResend = App::$app->session->get('last_resend_time');
        if ($lastResend && time() - $lastResend < 60) {
            App::$app->session->setFlash('error', 'Please wait 1 minute before resending.');
            $response->redirect('/verify');
            return;
        }

        App::$app->session->set('last_resend_time', time());

        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        CacheService::set('otp_' . $userId, password_hash($otp, PASSWORD_DEFAULT), 900);
        EmailService::sendVerificationCode($user, $otp, 'register');

        App::$app->session->setFlash('success', 'Verification code sent to your email.');
        $response->redirect('/verify');
    }
}
