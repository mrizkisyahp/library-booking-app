<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Middleware\AdminMiddleware;
use App\Models\Room;
use App\Core\App;

class AdminRoomController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware());
    }

    public function index()
    {
        $this->setTitle('Manage Rooms | Admin');
        $this->setLayout('main');

        // Get all rooms 
        $stmt = App::$app->db->prepare("SELECT * FROM rooms ORDER BY created_at DESC");
        $stmt->execute();
        $rooms = $stmt->fetchAll(\PDO::FETCH_CLASS, Room::class);

        return $this->render('admin/rooms/index', [
            'rooms' => $rooms
        ]);
    }

    // create new room
    public function create(Request $request, Response $response)
    {
        $room = new Room();
        $room->status = 'available'; 

        $this->setTitle('Create New Room | Admin');
        $this->setLayout('main');

        if ($request->isPost()) {
            // Validate CSRF token
            if (!\App\Core\Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                App::$app->session->setFlash('error', 'Invalid CSRF token.');
                return $this->render('admin/rooms/create', ['room' => $room]);
            }

            $room->loadData($request->getBody());

            // Additional validation
            if ($room->capacity_max < $room->capacity_min) {
                $room->addError('capacity_max', 'Maximum capacity must be greater than or equal to minimum capacity.');
            }

            if ($room->validate() && empty($room->errors)) {
                if ($room->save()) {
                    \App\Core\Services\Logger::admin('created a room', App::$app->user->id, "Room '{$room->title}' created");
  
                    App::$app->session->setFlash('success', 'Room created successfully!');
                    $response->redirect('/admin/rooms');
                    return;
                }
            }
        }

        return $this->render('admin/rooms/create', ['room' => $room]);
    }

    // edit room
    public function edit(Request $request, Response $response)
    {
        $roomId = $_GET['id'] ?? null;

        if (!$roomId) {
            App::$app->session->setFlash('error', 'Room not found.');
            $response->redirect('/admin/rooms');
            return;
        }

        $room = Room::findOne(['id' => $roomId]);

        if (!$room) {
            App::$app->session->setFlash('error', 'Room not found.');
            $response->redirect('/admin/rooms');
            return;
        }

        $this->setTitle('Edit Room | Admin');
        $this->setLayout('main');

        if ($request->isPost()) {
            // Validate CSRF token
            if (!\App\Core\Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                App::$app->session->setFlash('error', 'Invalid CSRF token.');
                return $this->render('admin/rooms/edit', ['room' => $room]);
            }

            $room->loadData($request->getBody());

            // Additional validation
            if ($room->capacity_max < $room->capacity_min) {
                $room->addError('capacity_max', 'Maximum capacity must be greater than or equal to minimum capacity.');
            }

            if ($room->validate() && empty($room->errors)) {
                // Update room
                $stmt = App::$app->db->prepare(
                    "UPDATE rooms SET title = :title, capacity_min = :capacity_min, 
                     capacity_max = :capacity_max, description = :description, 
                     status = :status, updated_at = NOW() WHERE id = :id"
                );
                
                $stmt->bindValue(':title', $room->title);
                $stmt->bindValue(':capacity_min', $room->capacity_min);
                $stmt->bindValue(':capacity_max', $room->capacity_max);
                $stmt->bindValue(':description', $room->description);
                $stmt->bindValue(':status', $room->status);
                $stmt->bindValue(':id', $room->id);
                
                if ($stmt->execute()) {
                    \App\Core\Services\Logger::admin('updated a room', App::$app->user->id, "Room '{$room->title}' created");

                    
                    App::$app->session->setFlash('success', 'Room updated successfully!');
                    $response->redirect('/admin/rooms');
                    return;
                }
            }
        }

        return $this->render('admin/rooms/edit', ['room' => $room]);
    }

    // delete room
    public function delete(Request $request, Response $response)
    {
        if (!$request->isPost()) {
            $response->redirect('/admin/rooms');
            return;
        }

        // Validate CSRF token
        if (!\App\Core\Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid CSRF token.');
            $response->redirect('/admin/rooms');
            return;
        }

        $roomId = $_POST['id'] ?? null;

        if (!$roomId) {
            App::$app->session->setFlash('error', 'Room not found.');
            $response->redirect('/admin/rooms');
            return;
        }

        $room = Room::findOne(['id' => $roomId]);

        if (!$room) {
            App::$app->session->setFlash('error', 'Room not found.');
            $response->redirect('/admin/rooms');
            return;
        }

        // Check if room has any bookings
        $stmt = App::$app->db->prepare("SELECT COUNT(*) as count FROM bookings WHERE room_id = :room_id");
        $stmt->bindValue(':room_id', $roomId);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            App::$app->session->setFlash('error', 
                'Cannot delete room with existing bookings. Set status to maintenance instead.');
            $response->redirect('/admin/rooms');
            return;
        }

        // Delete room
        $stmt = App::$app->db->prepare("DELETE FROM rooms WHERE id = :id");
        $stmt->bindValue(':id', $roomId);
        
        if ($stmt->execute()) {
            \App\Core\Services\Logger::admin('deleted a room', App::$app->user->id, "Room '{$room->title}' created");

            
            App::$app->session->setFlash('success', 'Room deleted successfully!');
        } else {
            App::$app->session->setFlash('error', 'Failed to delete room.');
        }

        $response->redirect('/admin/rooms');
    }
}
