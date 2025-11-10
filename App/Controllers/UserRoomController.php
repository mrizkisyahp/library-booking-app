<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Response;
use App\Core\Request;
use App\Core\Services\Logger;
use App\Core\Services\EmailService;
use App\Core\Middleware\AuthMiddleware;
use App\Models\Room;

class UserRoomController extends Controller {
    public function __construct() {
        $this->registerMiddleware(new AuthMiddleware());
    }

    public function index(Request $request, Response $response) {
        $this->setLayout('main');
        $this->setTitle('Room | Library Booking App');

        $filters = [
            'nama_ruangan' => $request->getBody()['nama_ruangan'] ?? null,
            'kapasitas_min' => $request->getBody()['kapasitas_min'] ?? null,
            'kapasitas_max' => $request->getBody()['kapasitas_max'] ?? null,
            'jenis_ruangan' => $request->getBody()['jenis_ruangan'] ?? null,
        ];

        $rooms = Room::search($filters);

        return $this->render('User/Rooms/Index', [
            'rooms' => $rooms,
            'filters' => $filters,
        ]);
    }

    public function show(Request $request, Response $response) {
        $id_ruangan = (int)($request->getBody()['id_ruangan'] ?? $request->getBody()['id'] ?? 0);
        if ($id_ruangan <= 0) {
            $response->redirect('/rooms');
            return;
        }

        $room = Room::findOne(['id_ruangan' => $id_ruangan]);
        if (!$room) {
            $response->redirect('/rooms');
            return;
        }

        $this->setLayout('main');
        $this->setTitle("Detail Ruangan | {$room->nama_ruangan}");

        return $this->render('User/Rooms/Show', [
            'room' => $room,
            'photos' => $room->getPhotoDataUris(),
            'facilities' => $room->getFacilities(),
            'availability' => $room->getAvailabilityCalendar(7),
            'user' => App::$app->user,
        ]);
    }
}
