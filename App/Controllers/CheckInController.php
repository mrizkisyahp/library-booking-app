<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Middleware\AuthMiddleware;
use App\Core\App;
use App\Models\Booking;

class CheckInController extends Controller
{
    /** Seconds before start time that check-in opens (10 minutes) */
    private const WINDOW_BEFORE = 10 * 60;

    /** Seconds after start time that check-in remains open (10 minutes) */
    private const WINDOW_AFTER  = 10 * 60;

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

    /**
     * Verify booking code and perform check-in
     */
    public function verify(Request $request, Response $response)
    {
        if (!$request->isPost()) {
            $response->redirect('/checkin');
            return;
        }

        // CSRF
        if (!\App\Core\Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid CSRF token.');
            $response->redirect('/checkin');
            return;
        }

        $bookingCode = trim($_POST['booking_code'] ?? '');
        if ($bookingCode === '') {
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

        // Ownership
        if ((int)$booking->user_id !== (int)App::$app->user->id) {
            App::$app->session->setFlash('error', 'This booking does not belong to you.');
            $response->redirect('/checkin');
            return;
        }

        // Status pre-checks
        // Expected normal flow: validated -> (check-in) -> active -> (check-out) -> completed
        if ($booking->status === 'active') {
            App::$app->session->setFlash('success', 'You are already checked in. Enjoy your booking!');
            $response->redirect('/my-bookings');
            return;
        }
        if ($booking->status === 'completed') {
            App::$app->session->setFlash('error', 'This booking is already completed.');
            $response->redirect('/my-bookings');
            return;
        }
        if ($booking->status === 'cancelled') {
            App::$app->session->setFlash('error', 'This booking has been cancelled.');
            $response->redirect('/my-bookings');
            return;
        }
        if ($booking->status !== 'validated') {
            App::$app->session->setFlash('error', 'Booking must be validated before check-in. Current status: ' . $booking->status);
            $response->redirect('/checkin');
            return;
        }

        // Time window checks
        $startTs = strtotime($booking->booking_date . ' ' . $booking->start_time);
        $now     = time();

        $openTs  = $startTs - self::WINDOW_BEFORE; // earliest allowed time
        $closeTs = $startTs + self::WINDOW_AFTER;  // latest allowed time

        if ($now < $openTs) {
            $minutesUntil = (int)ceil(($openTs - $now) / 60);
            App::$app->session->setFlash('error', "Check-in will be available in {$minutesUntil} minute(s).");
            $response->redirect('/checkin');
            return;
        }

        if ($now > $closeTs) {
            App::$app->session->setFlash('error', 'Check-in time has passed. Booking may be cancelled.');
            $response->redirect('/checkin');
            return;
        }

        // Perform check-in
        if ($booking->checkIn()) {
            \App\Core\Services\Logger::info('User checked in', [
                'user_id'      => App::$app->user->id,
                'booking_id'   => $booking->id,
                'booking_code' => $bookingCode
            ]);
            App::$app->session->setFlash('success', 'Check-in successful! Enjoy your booking.');
            $response->redirect('/my-bookings');
            return;
        }

        App::$app->session->setFlash('error', 'Failed to check-in. Please try again.');
        $response->redirect('/checkin');
    }

    /**
     * Perform check-out
     */
    public function checkout(Request $request, Response $response)
    {
        if (!$request->isPost()) {
            $response->redirect('/my-bookings');
            return;
        }

        // CSRF
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
        if (!$booking || (int)$booking->user_id !== (int)App::$app->user->id) {
            App::$app->session->setFlash('error', 'Booking not found.');
            $response->redirect('/my-bookings');
            return;
        }

        // Status checks
        if ($booking->status === 'completed') {
            App::$app->session->setFlash('success', 'This booking is already completed.');
            $response->redirect('/my-bookings');
            return;
        }
        if ($booking->status !== 'active') {
            App::$app->session->setFlash('error', 'You can only check-out from an active booking.');
            $response->redirect('/my-bookings');
            return;
        }

        if ($booking->checkOut()) {
            \App\Core\Services\Logger::info('User checked out', [
                'user_id'    => App::$app->user->id,
                'booking_id' => $booking->id
            ]);
            App::$app->session->setFlash('success', 'Check-out successful!');
        } else {
            App::$app->session->setFlash('error', 'Failed to check-out.');
        }

        $response->redirect('/my-bookings');
    }
}
