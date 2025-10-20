<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Middleware\AuthMiddleware;
use App\Models\Room;
use App\Core\App;

class RoomController extends Controller
{

    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware());
    }

    // view all room
    public function index()
    {
        $this->setTitle('Available Rooms | Library Booking App');
        $this->setLayout('main');
        
        $rooms = Room::getAvailableRooms();
        return $this->render('rooms/index', [
            'rooms' => $rooms
        ]);
    }

    /**
     * View single room details
     */
    public function view(Request $request)
    {
        $roomId = $_GET['id'] ?? null;

        if (!$roomId) {
            App::$app->session->setFlash('error', 'Room not found.');
            return (new Response())->redirect('/rooms');
        }

        $room = Room::findOne(['id' => $roomId]);

        if (!$room) {
            App::$app->session->setFlash('error', 'Room not found.');
            return (new Response())->redirect('/rooms');
        }

        $this->setTitle($room->title . ' | Library Booking App');
        $this->setLayout('main');

        // Pass room data to the view
        return $this->render('rooms/view', [
            'room' => $room
        ]);
    }
}
