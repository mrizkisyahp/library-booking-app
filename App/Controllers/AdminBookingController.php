<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Middleware\AdminMiddleware;
use App\Models\Booking;
use App\Models\User;
use App\Core\App;
use App\Core\Services\EmailService;

class AdminBookingController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware());
    }

    public function index()
    {
        $this->setTitle('Manage Bookings | Admin');
        $this->setLayout('main');

        // ambil booking dengan detail2nya
        $bookings = Booking::getAllBookingsWithDetails();

        return $this->render('admin/bookings/index', [
            'bookings' => $bookings
        ]);
    }

    // approve booking/validate booking
    public function validate(Request $request, Response $response)
    {
        if (!$request->isPost()) {
            $response->redirect('/admin/bookings');
            return;
        }

        // Validate CSRF token
        if (!\App\Core\Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid CSRF token.');
            $response->redirect('/admin/bookings');
            return;
        }

        $bookingId = $_POST['booking_id'] ?? null;

        if (!$bookingId) {
            App::$app->session->setFlash('error', 'Booking not found.');
            $response->redirect('/admin/bookings');
            return;
        }

        $booking = Booking::findOne(['id' => $bookingId]);

        if (!$booking) {
            App::$app->session->setFlash('error', 'Booking not found.');
            $response->redirect('/admin/bookings');
            return;
        }

        if ($booking->status !== 'pending') {
            App::$app->session->setFlash('error', 'Only pending bookings can be validated.');
            $response->redirect('/admin/bookings');
            return;
        }

        // Update status to validated
        $stmt = App::$app->db->prepare("UPDATE bookings SET status = 'validated' WHERE id = :id");
        $stmt->bindValue(':id', $bookingId);
        
        if ($stmt->execute()) {
            // Get user info for email
            $user = User::findOne(['id' => $booking->user_id]);
            
            // Send email notification
            EmailService::sendBookingValidated($user, $booking);
            
            \App\Core\Services\Logger::info('Booking validated', [
                'admin_id' => App::$app->user->id,
                'booking_id' => $bookingId,
                'user_id' => $booking->user_id
            ]);
            
            App::$app->session->setFlash('success', 'Booking validated successfully!');
        } else {
            App::$app->session->setFlash('error', 'Failed to validate booking.');
        }

        $response->redirect('/admin/bookings');
    }

    // cancel booking
    public function cancel(Request $request, Response $response)
    {
        if (!$request->isPost()) {
            $response->redirect('/admin/bookings');
            return;
        }

        // Validate CSRF token
        if (!\App\Core\Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid CSRF token.');
            $response->redirect('/admin/bookings');
            return;
        }

        $bookingId = $_POST['booking_id'] ?? null;
        $reason = $_POST['reason'] ?? 'Cancelled by admin';

        if (!$bookingId) {
            App::$app->session->setFlash('error', 'Booking not found.');
            $response->redirect('/admin/bookings');
            return;
        }

        $booking = Booking::findOne(['id' => $bookingId]);

        if (!$booking) {
            App::$app->session->setFlash('error', 'Booking not found.');
            $response->redirect('/admin/bookings');
            return;
        }

        // Update status to cancelled
        $stmt = App::$app->db->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = :id");
        $stmt->bindValue(':id', $bookingId);
        
        if ($stmt->execute()) {
            // Get user info for email
            $user = User::findOne(['id' => $booking->user_id]);
            
            // Send email notification
            EmailService::sendBookingCancelled($user, $booking, $reason);
            
            \App\Core\Services\Logger::info('Booking cancelled by admin', [
                'admin_id' => App::$app->user->id,
                'booking_id' => $bookingId,
                'user_id' => $booking->user_id,
                'reason' => $reason
            ]);
            
            App::$app->session->setFlash('success', 'Booking cancelled successfully!');
        } else {
            App::$app->session->setFlash('error', 'Failed to cancel booking.');
        }

        $response->redirect('/admin/bookings');
    }

    // tandain booking complete
    public function complete(Request $request, Response $response)
    {
        if (!$request->isPost()) {
            $response->redirect('/admin/bookings');
            return;
        }

        // Validate CSRF token
        if (!\App\Core\Csrf::validateToken($_POST['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'Invalid CSRF token.');
            $response->redirect('/admin/bookings');
            return;
        }

        $bookingId = $_POST['booking_id'] ?? null;

        if (!$bookingId) {
            App::$app->session->setFlash('error', 'Booking not found.');
            $response->redirect('/admin/bookings');
            return;
        }

        $booking = Booking::findOne(['id' => $bookingId]);

        if (!$booking) {
            App::$app->session->setFlash('error', 'Booking not found.');
            $response->redirect('/admin/bookings');
            return;
        }

        // Update status to completed
        $stmt = App::$app->db->prepare("UPDATE bookings SET status = 'completed' WHERE id = :id");
        $stmt->bindValue(':id', $bookingId);
        
        if ($stmt->execute()) {
            // Get user info for feedback email
            $user = User::findOne(['id' => $booking->user_id]);
            
            // Send feedback request email
            EmailService::sendFeedbackRequest($user, $booking);
            
            \App\Core\Services\Logger::info('Booking completed', [
                'admin_id' => App::$app->user->id,
                'booking_id' => $bookingId,
                'user_id' => $booking->user_id
            ]);
            
            App::$app->session->setFlash('success', 'Booking marked as completed! Feedback request sent to user.');
        } else {
            App::$app->session->setFlash('error', 'Failed to complete booking.');
        }

        $response->redirect('/admin/bookings');
    }
}
