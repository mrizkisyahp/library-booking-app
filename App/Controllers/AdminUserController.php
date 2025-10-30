<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\App;
use App\Models\User;
use App\Core\Middleware\AdminMiddleware;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware());
    }

    public function index()
    {
        $stmt = App::$app->db->prepare("SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC");
        $stmt->execute();
        $users = $stmt->fetchAll(\PDO::FETCH_CLASS, User::class);

        $this->setTitle('Manage Users | Admin');
        $this->setLayout('main');
        return $this->render('admin/users/index', ['users' => $users]);
    }

    public function updateStatus(Request $request, Response $response)
    {
        if (!$request->isPost()) {
            $response->redirect('/admin/users');
            return;
        }

        $userId = $_POST['user_id'] ?? null;
        $action = $_POST['action'] ?? null;

        if (!$userId || !$action) {
            App::$app->session->setFlash('error', 'Invalid request.');
            $response->redirect('/admin/users');
            return;
        }

        $user = User::findOne(['id' => $userId]);
        if (!$user) {
            App::$app->session->setFlash('error', 'User not found.');
            $response->redirect('/admin/users');
            return;
        }

        if ($action === 'verify_kubaca') {
            if (!$user->kubaca_img) {
                App::$app->session->setFlash('error', 'No KuBaca image to verify.');
                $response->redirect('/admin/users');
                return;
            }

            $stmt = App::$app->db->prepare("UPDATE users SET status = 'verified' WHERE id = :id");
            $stmt->bindValue(':id', $userId);
            $stmt->execute();

            \App\Core\Services\EmailService::sendKubacaVerified($user);
        } elseif ($action === 'reject_kubaca') {
            $stmt = App::$app->db->prepare("UPDATE users SET kubaca_img = NULL WHERE id = :id");
            $stmt->bindValue(':id', $userId);
            $stmt->execute();

            App::$app->session->setFlash('success', 'KuBaca image rejected. User can re-upload.');
        } elseif (in_array($action, ['suspend', 'activate'])) {
            $statusMap = [
                'suspend' => 'suspended',
                'activate' => 'active'
            ];
            
            $stmt = App::$app->db->prepare("UPDATE users SET status = :status WHERE id = :id");
            $stmt->bindValue(':status', $statusMap[$action]);
            $stmt->bindValue(':id', $userId);
            $stmt->execute();

            App::$app->session->setFlash('success', 'User status updated.');
        }

        $response->redirect('/admin/users');
    }
}
