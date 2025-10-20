<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Middleware\AuthMiddleware;
use App\Models\Booking;
use App\Models\Room;
use App\Core\App;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware());
    }

    // create new booking
    public function create(Request $request, Response $response)
    {
        $roomId = $_GET['room_id'] ?? null;

        if (!$roomId) {
            App::$app->session->setFlash('error', 'Please select a room to book.');
            $response->redirect('/rooms');
            return;
        }

        $room = Room::findOne(['id' => $roomId]);

        if (!$room) {
            App::$app->session->setFlash('error', 'Room not found.');
            $response->redirect('/rooms');
            return;
        }

        if (!$room->isAvailable()) {
            App::$app->session->setFlash('error', 'This room is not available for booking.');
            $response->redirect('/rooms');
            return;
        }

        $booking = new Booking();
        $booking->room_id = $room->id;

        $this->setTitle('Book Room: ' . $room->title . ' | Library Booking App');
        $this->setLayout('main');

        if ($request->isPost()) {
            // Validate CSRF token
            if (!\App\Core\Csrf::validateToken($_POST['csrf_token'] ?? '')) {
                App::$app->session->setFlash('error', 'Invalid CSRF token.');
                return $this->render('bookings/create', [
                    'booking' => $booking,
                    'room' => $room
                ]);
            }

            // Load form data
            $booking->loadData($request->getBody());
            $booking->room_id = $room->id; 

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/bookings/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = 'booking_' . time() . '_' . uniqid() . '.' . $extension;
                $uploadPath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $booking->image = $filename;
                }
            }

            // Validate basic rules
            if (!$booking->validate()) {
                return $this->render('bookings/create', [
                    'booking' => $booking,
                    'room' => $room
                ]);
            }

            // Validate business rules
            if (!$booking->validateBooking()) {
                return $this->render('bookings/create', [
                    'booking' => $booking,
                    'room' => $room
                ]);
            }

            // Save booking
            if ($booking->save()) {
                \App\Core\Services\Logger::info(
                    'Booking created',
                    [
                        'user_id' => App::$app->user->id,
                        'booking_id' => $booking->id,
                        'room_id' => $room->id,
                        'status' => $booking->status
                    ]
                );
                
                App::$app->session->setFlash('success', 
                    'Booking created successfully! Booking Code: ' . $booking->booking_code . 
                    '. Your booking is pending admin validation.');
                $response->redirect('/my-bookings');
                return;
            } else {
                App::$app->session->setFlash('error', 'Failed to create booking. Please try again.');
            }
        }

        return $this->render('bookings/create', [
            'booking' => $booking,
            'room' => $room
        ]);
    }

    // view user booking
    public function myBookings()
    {
        $this->setTitle('My Bookings | Library Booking App');
        $this->setLayout('main');

        $userId = App::$app->user->id;
        $bookings = Booking::getUserBookings($userId);

        return $this->render('bookings/my-bookings', [
            'bookings' => $bookings
        ]);
    }
}
