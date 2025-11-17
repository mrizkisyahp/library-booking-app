<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Middleware\AdminMiddleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Services\AdminUserService;
use App\Models\User;

class AdminUserController extends Controller
{
    private AdminUserService $service;
    private ?User $currentUser = null;

    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware());
        $this->service = new AdminUserService();
        $this->currentUser = App::$app->user instanceof User ? App::$app->user : null;
    }

    public function index()
    {
        $this->setLayout('main');
        $this->setTitle('Admin User Management | Library Booking App');

        $request = App::$app->request;
        $query = $request->getBody();
        $filters = [
            'keyword' => $query['keyword'] ?? null,
            'role' => $query['role'] ?? null,
            'status' => $query['status'] ?? null,
            'page' => (int)($query['page'] ?? ($_GET['page'] ?? 1)),
            'perPage' => 20,
        ];

        $result = $this->service->listUsers($filters);
        $data = $result['data'] ?? [];
        $stats = $data['stats'] ?? [];

        return $this->render('Admin/Users/Index', [
            'users' => $data['users'] ?? [],
            'filters' => $data['filters'] ?? $filters,
            'stats' => $stats,
            'currentPage' => $data['currentPage'] ?? $filters['page'],
            'perPage' => $data['perPage'] ?? $filters['perPage'],
            'totalUsers' => $stats['total'] ?? count($data['users'] ?? []),
            'roles' => $this->service->getRoles(),
            'statuses' => $this->service->getStatusOptions(),
        ]);
    }

    public function create()
    {
        $this->setLayout('main');
        $this->setTitle('Create User | Library Booking App');

        $model = new User();
        $model->status = 'pending verification';

        return $this->render('Admin/Users/Create', [
            'model' => $model,
            'roles' => $this->service->getRoles(),
            'statuses' => $this->service->getStatusOptions(),
        ]);
    }

    public function store(Request $request, Response $response)
    {
        if (!$request->isPost() || !Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid request.');
            $response->redirect('/admin/users');
            return;
        }

        $result = $this->service->createUser($request->getBody(), $this->currentUser?->id_user);
        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');

        if ($result['success']) {
            $response->redirect('/admin/users');
            return;
        }

        $this->setLayout('main');
        return $this->render('Admin/Users/Create', [
            'model' => $result['data']['model'] ?? new User(),
            'roles' => $this->service->getRoles(),
            'statuses' => $this->service->getStatusOptions(),
        ]);
    }

    public function edit(Request $request, Response $response)
    {
        $this->setLayout('main');
        $this->setTitle('Edit User | Library Booking App');

        $id = (int)($request->getBody()['id_user'] ?? $request->getBody()['id'] ?? 0);
        $user = $this->service->getUserById($id);
        if (!$user) {
            App::$app->session->setFlash('error', 'User not found.');
            $response->redirect('/admin/users');
            return;
        }

        return $this->render('Admin/Users/Edit', [
            'model' => $user,
            'roles' => $this->service->getRoles(),
            'statuses' => $this->service->getStatusOptions(),
        ]);
    }

    public function show(Request $request, Response $response)
    {
        $this->setLayout('main');
        $this->setTitle('User Detail | Library Booking App');

        $id = (int)($request->getBody()['id_user'] ?? $request->getBody()['id'] ?? 0);
        $user = $this->service->getUserById($id);
        if (!$user) {
            App::$app->session->setFlash('error', 'User not found.');
            $response->redirect('/admin/users');
            return;
        }

        return $this->render('Admin/Users/Show', [
            'user' => $user,
            'statuses' => $this->service->getStatusOptions(),
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
        $result = $this->service->updateUser($id, $body, $this->currentUser?->id_user);

        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');

        if ($result['success']) {
            $response->redirect('/admin/users');
            return;
        }

        $this->setLayout('main');
        return $this->render('Admin/Users/Edit', [
            'model' => $result['data']['model'] ?? $this->service->getUserById($id),
            'roles' => $this->service->getRoles(),
            'statuses' => $this->service->getStatusOptions(),
        ]);
    }

    public function delete(Request $request, Response $response)
    {
        if (!$request->isPost() || !Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid request.');
            $response->redirect('/admin/users');
            return;
        }

        $id = (int)($request->getBody()['id_user'] ?? 0);
        $result = $this->service->deleteUser($id, $this->currentUser?->id_user);
        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');
        $response->redirect('/admin/users');
    }

    public function suspend(Request $request, Response $response)
    {
        $this->handleQuickAction($request, $response, fn (int $id) => $this->service->suspendUser($id, $this->currentUser?->id_user));
    }

    public function unsuspend(Request $request, Response $response)
    {
        $this->handleQuickAction($request, $response, fn (int $id) => $this->service->unsuspendUser($id, $this->currentUser?->id_user));
    }

    public function resetPassword(Request $request, Response $response)
    {
        $this->handleQuickAction($request, $response, fn (int $id) => $this->service->resetPassword($id, $this->currentUser?->id_user));
    }

    public function approveKubaca(Request $request, Response $response)
    {
        $this->handleQuickAction($request, $response, fn (int $id) => $this->service->approveKubaca($id, $this->currentUser?->id_user));
    }

    public function rejectKubaca(Request $request, Response $response)
    {
        $body = $request->getBody();
        $reason = $body['reason'] ?? null;
        $this->handleQuickAction(
            $request,
            $response,
            fn (int $id) => $this->service->rejectKubaca($id, $reason, $this->currentUser?->id_user)
        );
    }

    private function handleQuickAction(Request $request, Response $response, callable $action): void
    {
        if (!$request->isPost() || !Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid request.');
            $response->redirect('/admin/users');
            return;
        }

        $id = (int)($request->getBody()['id_user'] ?? 0);
        $result = $action($id);
        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');
        $response->redirect('/admin/users');
    }
}
