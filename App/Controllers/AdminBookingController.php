<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Middleware\AdminMiddleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Services\Logger;
use App\Models\Booking;
use App\Models\User;

class AdminBookingController extends Controller {
    protected ?User $currentUser = null;
    public function __construct() {
        $this->registerMiddleware(new AdminMiddleware());
        $this->currentUser = App::$app->user instanceof User ? App::$app->user : null;
    }

    public function index() {
        Booking::expireStaleDrafts();
        $this->setLayout('main');
        $this->setTitle('Manajemen Booking | Library Booking App');

        $pending = App::$app->db->pdo
            ->query("SELECT * FROM booking ORDER BY created_at DESC")
            ->fetchAll(\PDO::FETCH_ASSOC);

        return $this->render('Admin/Bookings/Index', [
            'bookings' => $pending,
        ]);
    }

    public function verify(Request $request, Response $response) {
        $admin = $this->currentUser;
        $id_booking = (int)($request->getBody()['booking_id']);
        $booking = Booking::findOne($id_booking);

        if (!$booking || $booking->status !== 'pending') {
            App::$app->session->setFlash('error', 'Booking tidak valid');
            $response->redirect('/admin/bookings');
            return;
        }

        $booking->status = 'verified';
        if (!$booking->checkin_code) {
            $booking->checkin_code = $this->generateCheckinCode();
        }
        $booking->save();

        Logger::admin('verified booking', (int)$admin->id_user, "Booking #{$id_booking} verified");
        App::$app->session->setFlash('success', 'Booking disetujui.');
        $response->redirect('/admin/bookings');
    }

    public function complete(Request $request, Response $response) {
        $admin = $this->currentUser;
        $id_booking = (int)($request->getBody()['booking_id']);
        $booking = Booking::findOne($id_booking);

        if (!$booking || $booking->status !== 'active') {
            App::$app->session->setFlash('error', 'Booking tidak valid.');
            $response->redirect('/admin/bookings');
            return;
        }

        $booking->status = 'completed';
        $booking->save();

        Logger::admin('completed booking', (int)$admin->id_user, "Booking #{$id_booking} marked as completed");
        App::$app->session->setFlash('success', 'Booking selesai.');
        $response->redirect('/admin/bookings');
    }

    private function generateCheckinCode(): string
    {
        do {
            $code = strtoupper(bin2hex(random_bytes(4)));
            $exists = Booking::findOne(['checkin_code' => $code]);
        } while ($exists);

        return $code;
    }
}
