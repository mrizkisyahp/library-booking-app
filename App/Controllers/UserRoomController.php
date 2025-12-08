<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Response;
use App\Core\Request;
use App\Core\Middleware\AuthMiddleware;
use App\Models\Room;
use App\Models\User;

class UserRoomController extends Controller
{
    protected ?User $currentUser = null;
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware());
        $this->currentUser = App::$app->user instanceof User ? App::$app->user : null;
    }

    public function index(Request $request, Response $response)
    {
        $this->setLayout('main');
        $this->setTitle('Room | Library Booking App');

        $perPage = 20;
        $page = (int) ($_GET['page'] ?? 1);
        $page = max(1, $page);

        $filters = [
            'nama_ruangan' => $request->getQuery()['nama_ruangan'] ?? null,
            'tanggal' => $request->getQuery()['tanggal'] ?? null,
            'waktu_mulai' => $request->getQuery()['waktu_mulai'] ?? null,
            'kapasitas_min' => $request->getQuery()['kapasitas_min'] ?? null,
            'kapasitas_max' => $request->getQuery()['kapasitas_max'] ?? null,

            // 'jenis_ruangan' => $request->getQuery()['jenis_ruangan'] ?? null,
            'jenis_ruangan' => $request->getBody()['jenis_ruangan'] ?? null,
        ];

        $rooms = Room::findPaginated($page, $perPage, $filters, [
            'only_available' => empty($filters['status_ruangan']),
        ]);

        // $debug = [
        //     'rooms' => $rooms,
        //     'filters' => $filters,
        //     'user' => $this->currentUser,
        //     'currentPage' => $page,
        //     'perPage' => $perPage,
        //     'totalRooms' => Room::count(),
        // ];

        // echo '<pre>';
        // print_r($debug);
        // echo '</pre>';
        // die();

        return $this->render('User/Rooms/Index', [
            'rooms' => $rooms,
            'filters' => $filters,
            'user' => $this->currentUser,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalRooms' => Room::count(),
        ]);
    }

    public function show(Request $request, Response $response)
    {

        $id_ruangan = (int) ($request->getBody()['id_ruangan'] ?? $request->getBody()['id'] ?? 0);
        if ($id_ruangan <= 0) {
            $response->redirect('/rooms');
            return;
        }

        $room = Room::Query()->where('id_ruangan', $id_ruangan)->first();
        if (!$room) {
            $response->redirect('/rooms');
            return;
        }

        $this->setLayout('main');
        $this->setTitle("Detail Ruangan | {$room->nama_ruangan}");

        // echo '<pre>';
        // print_r([
        //     'room' => $room,
        //     'photos' => $room->getPhotoDataUris(),
        //     'facilities' => $room->getFacilities(),
        //     'availability' => $room->getAvailabilityCalendar(7),
        //     'user' => $this->currentUser
        // ]);
        // echo '</pre>';
        // die();

        return $this->render('User/Rooms/Show', [
            'room' => $room,
            'photos' => $room->getPhotoDataUris(),
            'facilities' => $room->getFacilities(),
            'availability' => $room->getAvailabilityCalendar(7),
            'user' => $this->currentUser,
        ]);
    }
}
