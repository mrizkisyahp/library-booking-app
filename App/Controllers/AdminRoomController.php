<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Middleware\AdminMiddleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Services\AdminRoomService;
use App\Models\Room;

class AdminRoomController extends Controller
{
    private AdminRoomService $service;

    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware());
    }

    public function index(Request $request, Response $response)
    {
        $this->setLayout('main');
        $this->setTitle('Admin Room Management | Library Booking App');

        $filters = [
            'keyword' => $request->getBody()['keyword'] ?? null,
            'jenis_ruangan' => $request->getBody()['jenis_ruangan'] ?? null,
            'status_ruangan' => $request->getBody()['status_ruangan'] ?? null,
            'page' => (int)($request->getBody()['page'] ?? 1),
        ];

        $service = new AdminRoomService();
        $result = $service->listRooms($filters);
        $data = $result['data'] ?? [];

        return $this->render('Admin/Rooms/Index', [
            'rooms' => $data['rooms'] ?? [],
            'filters' => $filters,
            'statusOptions' => $data['statusOptions'] ?? $service->getStatusOptions(),
            'totalRooms' => $data['total'] ?? 0,
            'currentPage' => $data['currentPage'] ?? $filters['page'],
            'perPage' => $data['perPage'] ?? 20,
        ]);
    }

    public function create()
    {
        $this->setLayout('main');
        $this->setTitle('Create Room | Library Booking App');

        $service = new AdminRoomService();

        return $this->render('Admin/Rooms/Create', [
            'room' => new Room(),
            'statusOptions' => $service->getStatusOptions(),
        ]);
    }

    public function store(Request $request, Response $response)
    {
        if (!$request->isPost() || !Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid request.');
            $response->redirect('/admin/rooms');
            return;
        }

        $service = new AdminRoomService();
        $result = $service->createRoom($request->getBody());
        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');

        if ($result['success']) {
            $response->redirect('/admin/rooms');
            return;
        }

        $this->setLayout('main');
        return $this->render('Admin/Rooms/Create', [
            'room' => $result['data']['room'] ?? new Room(),
            'statusOptions' => $service->getStatusOptions(),
        ]);
    }

    public function edit(Request $request, Response $response)
    {
        $this->setLayout('main');
        $this->setTitle('Edit Room | Library Booking App');

        $id = (int)($request->getBody()['id_ruangan'] ?? $request->getBody()['id'] ?? 0);
        $service = new AdminRoomService();
        $room = $service->getRoomById($id);
        if (!$room) {
            App::$app->session->setFlash('error', 'Room not found.');
            $response->redirect('/admin/rooms');
            return;
        }

        return $this->render('Admin/Rooms/Edit', [
            'room' => $room,
            'statusOptions' => $service->getStatusOptions(),
        ]);
    }

    public function update(Request $request, Response $response)
    {
        if (!$request->isPost() || !Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid request.');
            $response->redirect('/admin/rooms');
            return;
        }

        $body = $request->getBody();
        $id = (int)($body['id_ruangan'] ?? 0);
        $service = new AdminRoomService();
        $result = $service->updateRoom($id, $body);

        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');

        if ($result['success']) {
            $response->redirect('/admin/rooms');
            return;
        }

        $this->setLayout('main');
        return $this->render('Admin/Rooms/Edit', [
            'room' => $result['data']['room'] ?? $service->getRoomById($id),
            'statusOptions' => $service->getStatusOptions(),
        ]);
    }

    public function delete(Request $request, Response $response)
    {
        if (!$request->isPost() || !Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid request.');
            $response->redirect('/admin/rooms');
            return;
        }

        $id = (int)($request->getBody()['id_ruangan'] ?? 0);
        $service = new AdminRoomService();
        $result = $service->deleteRoom($id);
        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');
        $response->redirect('/admin/rooms');
    }

    public function activate(Request $request, Response $response)
    {
        $this->handleStatusAction($request, $response, 'activateRoom');
    }

    public function deactivate(Request $request, Response $response)
    {
        $this->handleStatusAction($request, $response, 'deactivateRoom');
    }

    public function show(Request $request, Response $response)
    {
        $this->setLayout('main');
        $this->setTitle('Room Detail | Library Booking App');

        $id = (int)($request->getBody()['id_ruangan'] ?? $request->getBody()['id'] ?? 0);
        $service = new AdminRoomService();
        $room = $service->getRoomById($id);
        if (!$room) {
            App::$app->session->setFlash('error', 'Room not found.');
            $response->redirect('/admin/rooms');
            return;
        }

        return $this->render('Admin/Rooms/Show', [
            'room' => $room,
            'statusOptions' => $service->getStatusOptions(),
        ]);
    }

    private function handleStatusAction(Request $request, Response $response, string $method): void
    {
        if (!$request->isPost() || !Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid request.');
            $response->redirect('/admin/rooms');
            return;
        }

        $id = (int)($request->getBody()['id_ruangan'] ?? 0);
        $service = new AdminRoomService();
        $result = $service->{$method}($id);
        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');
        $response->redirect('/admin/rooms');
    }
}
