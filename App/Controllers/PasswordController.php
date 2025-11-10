<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\App;
use App\Models\User;
use App\Models\PasswordResetForm;
use App\Core\Services\EmailService;
use App\Core\Services\CacheService;
use App\Core\Services\Logger;

class PasswordController extends Controller
{
    public function forgot(Request $request, Response $response)
    {
        $this->setLayout('auth');
        $this->setTitle('Forgot Password | Library Booking App');

        $model = new PasswordResetForm();

        if ($request->isPost()) {
            $model->loadData($request->getBody());
            $model->mode = 'request';
            
            if (!$model->validate()) {
                return $this->render('ResetPassword/Forgot', ['model' => $model]);
            }

            $user = User::findOne(['email' => trim($model->email)]);
            if (!$user) {
                App::$app->session->setFlash('error', 'Email not found.');
                return $this->render('ResetPassword/Forgot', ['model' => $model]);
            }

            $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            CacheService::set('reset_otp_' . $user->id_user, password_hash($otp, PASSWORD_DEFAULT), 900);
            EmailService::sendVerificationCode($user, $otp, 'reset_password');

            App::$app->session->set('reset_user_id', $user->id_user);
            App::$app->session->setFlash('success', 'Reset code sent to your email.');
            $response->redirect('/reset');
            return;
        }

        return $this->render('ResetPassword/Forgot', ['model' => $model]);
    }

    public function reset(Request $request, Response $response)
    {
        $this->setLayout('auth');
        $this->setTitle('Reset Password | Library Booking App');

        $model = new PasswordResetForm();

        $userId = App::$app->session->get('reset_user_id');
        if (!$userId) {
            App::$app->session->setFlash('error', 'Session expired.');
            $response->redirect('/forgot');
            return;
        }

        $user = User::findOne(['id_user' => $userId]);
        if (!$user) {
            App::$app->session->setFlash('error', 'User not found.');
            $response->redirect('/forgot');
            return;
        }

        if ($request->isPost()) {
            $model->loadData($request->getBody());
            $model->mode = 'reset';
            
            if (!$model->validate()) {
                return $this->render('ResetPassword/Reset', ['model' => $model]);
            }

            $cachedHash = CacheService::get('reset_otp_' . $userId);
            if (!$cachedHash || !password_verify(trim($model->code), $cachedHash)) {
                App::$app->session->setFlash('error', 'Invalid or expired code.');
                return $this->render('ResetPassword/Reset', ['model' => $model]);
            }

            $hash = password_hash($model->password, PASSWORD_DEFAULT);
            $stmt = App::$app->db->prepare("UPDATE users SET password = :pass WHERE id_user = :id");
            $stmt->bindValue(':pass', $hash);
            $stmt->bindValue(':id', $user->id_user);
            $stmt->execute();

            CacheService::delete('reset_otp_' . $userId);
            App::$app->session->remove('reset_user_id');
            Logger::auth('password reset', $user->id_user, "Password reset via email");
            App::$app->session->setFlash('success', 'Password reset successful!');
            $response->redirect('/login');
            return;
        }

        return $this->render('ResetPassword/Reset', ['model' => $model]);
    }
}
