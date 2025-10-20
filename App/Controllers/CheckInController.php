<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Middleware\AuthMiddleware;
use App\Models\Booking;
use App\Core\App;

class CheckInController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware());
    }

    public function index()
    {
        $this->setTitle('Check-in | Library Booking App');
        $this->setLayout('main');

        return $this->render('checkin/index');
    }

    // verify booking and check in
    public function verify(Request $request, Response $response)
    {
        if (!$request->isPost()) {
            $response->redirect('/checkin');
            return;
        }

        // Validate CSRF token
        if (!\App\Core\Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid CSRF token.');
            $response->redirect('/checkin');
            return;
        }

        $bookingCode = trim($_POST['booking_code'] ?? '');

        if (empty($bookingCode)) {
            App::$app->session->setFlash('error', 'Please enter booking code.');
            $response->redirect('/checkin');
            return;
        }

        // Find booking by code
        $booking = Booking::findOne(['booking_code' => $bookingCode]);

        if (!$booking) {
            App::$app->session->setFlash('error', 'Invalid booking code.');
            $response->redirect('/checkin');
            return;
        }

        // Check if booking belongs to current user
        if ($booking->user_id != App::$app->user->id) {
            App::$app->session->setFlash('error', 'This booking does not belong to you.');
            $response->redirect('/checkin');
            return;
        }

        // Check if booking is validated
        if ($booking->status !== 'validated') {
            App::$app->session->setFlash('error', 'Booking must be validated before check-in. Current status: ' . $booking->status);
            $response->redirect('/checkin');
            return;
        }

        // Check if it's time to check-in (within 10 minutes before start time)
        $bookingDateTime = strtotime($booking->booking_date . ' ' . $booking->start_time);
        $now = time();
        $tenMinutesBefore = $bookingDateTime - (10 * 60);

        if ($now < $tenMinutesBefore) {
            $minutesUntil = ceil(($tenMinutesBefore - $now) / 60);
            App::$app->session->setFlash('error', "Check-in will be available in {$minutesUntil} minutes.");
            $response->redirect('/checkin');
            return;
        }

        if ($now > $bookingDateTime) {
            App::$app->session->setFlash('error', 'Check-in time has passed. Booking may be cancelled.');
            $response->redirect('/checkin');
            return;
        }

        // Perform check-in
        if ($booking->checkIn()) {
            \App\Core\Services\Logger::info('User checked in', [
                'user_id' => App::$app->user->id,
                'booking_id' => $booking->id,
                'booking_code' => $bookingCode
            ]);

            App::$app->session->setFlash('success', 'Check-in successful! Enjoy your booking.');
            $response->redirect('/my-bookings');
        } else {
            App::$app->session->setFlash('error', 'Failed to check-in. Please try again.');
            $response->redirect('/checkin');
        }
    }

    // checkout
    public function checkout(Request $request, Response $response)
    {
        if (!$request->isPost()) {
            $response->redirect('/my-bookings');
            return;
        }

        // Validate CSRF token
        if (!\App\Core\Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid CSRF token.');
            $response->redirect('/my-bookings');
            return;
        }

        $bookingId = $_POST['booking_id'] ?? null;

        if (!$bookingId) {
            App::$app->session->setFlash('error', 'Booking not found.');
            $response->redirect('/my-bookings');
            return;
        }

        $booking = Booking::findOne(['id' => $bookingId]);

        if (!$booking || $booking->user_id != App::$app->user->id) {
            App::$app->session->setFlash('error', 'Booking not found.');
            $response->redirect('/my-bookings');
            return;
        }

        if ($booking->checkOut()) {
            \App\Core\Services\Logger::info('User checked out', [
                'user_id' => App::$app->user->id,
                'booking_id' => $booking->id
            ]);

            App::$app->session->setFlash('success', 'Check-out successful!');
        } else {
            App::$app->session->setFlash('error', 'Failed to check-out.');
        }

        $response->redirect('/my-bookings');
    }
}
