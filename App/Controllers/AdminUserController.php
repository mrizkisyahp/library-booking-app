<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\Controller;
use App\Core\Middleware\AdminMiddleware;
use App\Models\User;
use App\Core\Services\Logger;
use App\Models\Role;
use App\Core\Services\FileUploaderService;
use App\Core\Csrf;

class AdminUserController extends Controller {
    protected ?User $currentUser = null;

    public function __construct() {
        $this->registerMiddleware(new AdminMiddleware());
        $this->currentUser = App::$app->user instanceof User ? App::$app->user : null;
    }

    public function index() {
        $this->setLayout('main');
        $this->setTitle('Admin User Management | Library Booking App');

        $perPage = 20;
        $page = (int)($_GET['page'] ?? 1);
        $page = max(1, $page);

        $users = User::findPaginated($page, $perPage);
        // echo '<pre>';
        // print_r($users);
        // echo '</pre>';
        // exit;
        return $this->render('Admin/Users/Index', [
            'users' => $users,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalUsers' => User::count(),
            'totalActive' => User::countActive(),
            'totalPending' => User::countPending(),
            'totalSuspended' => User::countSuspended()
        ]);
    }

    public function create() {
        $this->setLayout('main');
        $this->setTitle('Admin: Create New User | Library Booking App');

        $roles = Role::getAllRoleName();
        $model = new User();
        $model->status = 'active';
        return $this->render('Admin/Users/Create', [
            'roles' => $roles,
            'model' => $model
        ]);
    }

    public function store(Request $request, Response $response) {
        $user = new User();
        $user->setScenario(User::SCENARIO_REGISTER);

        if ($request->isPost()) {
            if (!Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                App::$app->session->setFlash('error', 'Invalid CSRF token.');
                return $this->render('Admin/Users/Create', ['model' => $user]);
            }
        }
        
        $postData = $request->getBody();
        $plainPassword = $postData['password'];
        
        $user->loadData($postData);
        
        $roleName = $postData['role'];
        $user->id_role = Role::getIdByName($roleName);
        
        if (empty($plainPassword)) {
            $plainPassword = 'password123';
        }
        
        $user->password = $plainPassword;
        $user->confirm_password = $plainPassword;

        if (empty($user->status)) {
            $user->status = 'active';
        }

        if ($user->validate()) {
            $user->password = password_hash($plainPassword, PASSWORD_DEFAULT);
            
            if ($user->save()) {
                if (!empty($_FILES['foto_kubaca']) && $_FILES['foto_kubaca']['error'] === UPLOAD_ERR_OK) {

                    $uploadResult = FileUploaderService::upload(
                    $_FILES['foto_kubaca'],
                    App::$ROOT_DIR . '/public/uploads/kubaca/',
                    ['image/jpeg', 'image/png', 'image/webp'],
                    2,
                    'kubaca_' . $user->id_user
                );

                if ($uploadResult['success']) {

                    // Update DB with filename
                    $stmt = App::$app->db->prepare("
                        UPDATE users SET kubaca_img = :img WHERE id_user = :id
                    ");
                    $stmt->bindValue(':img', $uploadResult['filename']);
                    $stmt->bindValue(':id', $user->id_user, \PDO::PARAM_INT);
                    $stmt->execute();

                    // Keep in model
                    $user->kubaca_img = $uploadResult['filename'];
                } else {
                    App::$app->session->setFlash('error', $uploadResult['error']);
                }
            }
                Logger::auth('created_by_admin', $user->id_user, "Email: {$user->email}, Role: {$roleName}, Status: {$user->status}");
                App::$app->session->setFlash('success', 'User created successfully!');
                $response->redirect('/admin/users');
                return;
            }
        }

        $this->setLayout('main');
        $roles = Role::getAllRoleName();
        return $this->render('Admin/Users/Create', [
            'model' => $user,
            'roles' => $roles
        ]);
    }

    public function edit(Request $request, Response $response) {
        $this->setLayout('main');
        $this->setTitle('Edit User | Library Booking App');

        $id_user = (int)($request->getBody()['id_user'] ?? $request->getBody()['id'] ?? 0);
        if ($id_user <= 0) {
            App::$app->session->setFlash('error', 'Invalid user ID.');
            $response->redirect('/admin/users');
            return;
        }

        $model = User::findOne(['id_user' => $id_user]);
        if (!$model) {
            App::$app->session->setFlash('error', 'User not found.');
            $response->redirect('/admin/users');
            return;
        }

        $roles = Role::getAllRoleName();

        return $this->render('Admin/Users/Edit', [
            'model' => $model,
            'roles' => $roles,
            'kubacaPreview' => $model->kubaca_img ? '/uploads/kubaca/' . $model->kubaca_img : null,
        ]);
    }

    public function update(Request $request, Response $response)
    {
        if (!$request->isPost() || !Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid request.');
            $response->redirect('/admin/users');
            return;
        }

        $body = $request->getBody();
        $id = (int)($body['id_user'] ?? 0);
        $user = $id > 0 ? User::findOne(['id_user' => $id]) : null;
        if (!$user) {
            App::$app->session->setFlash('error', 'User not found.');
            $response->redirect('/admin/users');
            return;
        }

        $user->setScenario(User::SCENARIO_UPDATE);
        $roleName = $body['role'] ?? null;
        $body['id_role'] = $roleName ? Role::getIdByName($roleName) : $user->id_role;

        $newPassword = trim($body['password'] ?? '');
        unset($body['password'], $body['confirm_password']);
        $user->loadData($body);

        if ($newPassword !== '') {
            $user->password = $newPassword;
            $user->confirm_password = $newPassword;
        } else {
            $user->password = $user->confirm_password = '';
        }

        $uploadError = null;
        if ($user->validate()) {
            if ($newPassword !== '') {
                $user->password = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            if (!empty($_FILES['foto_kubaca']) && $_FILES['foto_kubaca']['error'] === UPLOAD_ERR_OK) {
                $upload = FileUploaderService::upload(
                    $_FILES['foto_kubaca'],
                    App::$ROOT_DIR . '/public/uploads/kubaca/',
                    ['image/jpeg', 'image/png', 'image/webp'],
                    2,
                    'kubaca_' . $user->id_user
                );

                if ($upload['success']) {
                    $user->kubaca_img = $upload['filename'];
                } else {
                    $uploadError = $upload['error'];
                }
            }

            if (!$uploadError && $user->save()) {
                Logger::auth('updated_by_admin', $user->id_user, "Email: {$user->email}, Role: {$roleName}, Status: {$user->status}");
                App::$app->session->setFlash('success', 'User updated successfully.');
                $response->redirect('/admin/users');
                return;
            }
        }

        if ($uploadError) {
            App::$app->session->setFlash('error', $uploadError);
        }

        $roles = Role::getAllRoleName();
        $this->setLayout('main');
        $this->setTitle('Edit User | Library Booking App');

        return $this->render('Admin/Users/Edit', [
            'model' => $user,
            'roles' => $roles,
            'kubacaPreview' => $user->kubaca_img ? '/uploads/kubaca/' . $user->kubaca_img : null,
        ]);
    }

    public function delete(Request $request, Response $response) {
        if (!$request->isPost() || !Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid request.');
            $response->redirect('/admin/users');
            return;
        }

        $id = (int)($request->getBody()['id_user'] ?? 0);
        if ($id <= 0) {
            App::$app->session->setFlash('error', 'Invalid user ID.');
            $response->redirect('/admin/users');
            return;
        }

        if ($this->currentUser && (int)$this->currentUser->id_user === $id) {
            App::$app->session->setFlash('error', 'You cannot delete your own account.');
            $response->redirect('/admin/users');
            return;
        }

        $user = User::findOne(['id_user' => $id]);
        if (!$user) {
            App::$app->session->setFlash('error', 'User not found.');
            $response->redirect('/admin/users');
            return;
        }

        if ($user->delete()) {
            Logger::auth('deleted_by_admin', $user->id_user, "Email: {$user->email}");
            App::$app->session->setFlash('success', 'User deleted successfully.');
        } else {
            App::$app->session->setFlash('error', 'Failed to delete user.');
        }

        $response->redirect('/admin/users');
    }

    public function approveKubaca(Request $request, Response $response) {
        $this->handleKubacaAction($request, $response, 'approved');
    }

    public function rejectKubaca(Request $request, Response $response) {
        $this->handleKubacaAction($request, $response, 'rejected');
    }

    private function handleKubacaAction(Request $request, Response $response, string $targetStatus): void {
        if (!$request->isPost() || !Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid request.');
            $response->redirect('/admin/users');
            return;
        }

        $id = (int)($request->getBody()['id_user'] ?? 0);
        if ($id <= 0) {
            App::$app->session->setFlash('error', 'Invalid user ID.');
            $response->redirect('/admin/users');
            return;
        }
         
        $user = User::findOne(['id_user' => $id]);
        if (!$user) {
            App::$app->session->setFlash('error', 'User not found.');
            $response->redirect('/admin/users');
            return;
        }

        if (!$user->kubaca_img) {
            App::$app->session->setFlash('error', 'User has no KuBaca upload.');
            $response->redirect('/admin/users');
            return;
        }

        $user->status = $targetStatus === 'approved' ? 'active' : 'rejected';
        if ($user->save()) {
            Logger::auth($targetStatus === 'approved' ? 'kubaca_approved' : 'kubaca_rejected', $user->id_user, "Admin {$this->currentUser?->email} updated status.");
            App::$app->session->setFlash('success', $targetStatus === 'approved' ? 'KuBaca approved.' : 'KuBaca rejected.');
        } else {
            App::$app->session->setFlash('error', 'Failed to update status.');
        }

        $response->redirect('/admin/users');
    }
}
