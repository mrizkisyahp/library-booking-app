<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Middleware\AdminMiddleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Services\BookingService;
use App\Models\User;

class AdminBookingController extends Controller {
    protected ?User $currentUser = null;
    public function __construct() {
        $this->registerMiddleware(new AdminMiddleware());
        $this->currentUser = App::$app->user instanceof User ? App::$app->user : null;
    }

    public function index() {
        $service = new BookingService();
        $this->setLayout('main');
        $this->setTitle('Manajemen Booking | Library Booking App');

        $bookings = $service->listAllBookings();

        return $this->render('Admin/Bookings/Index', [
            'bookings' => $bookings,
        ]);
    }

    public function detail(Request $request, Response $response)
    {
        $bookingId = (int)($request->getBody()['id'] ?? $request->getBody()['id_booking'] ?? 0);
        if ($bookingId <= 0) {
            App::$app->session->setFlash('error', 'ID booking tidak valid.');
            $response->redirect('/admin/bookings');
            return;
        }

        $service = new BookingService();
        $result = $service->getAdminBookingDetail($bookingId);
        if (!$result['success']) {
            App::$app->session->setFlash('error', $result['message'] ?? 'Booking tidak dapat ditampilkan.');
            $response->redirect('/admin/bookings');
            return;
        }

        $this->setLayout('main');
        $this->setTitle('Detail Booking | Library Booking App');

        return $this->render('Admin/Bookings/Detail', $result['data']);
    }

    public function verify(Request $request, Response $response) {
        $admin = $this->currentUser;
        $id_booking = (int)($request->getBody()['booking_id']);

        $service = new BookingService();
        $result = $service->verifyBooking($id_booking, (int)$admin->id_user);

        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');
        $response->redirect('/admin/bookings');
    }

    public function complete(Request $request, Response $response) {
        $admin = $this->currentUser;
        $id_booking = (int)($request->getBody()['booking_id']);

        $service = new BookingService();
        $result = $service->markBookingCompleted($id_booking, (int)$admin->id_user);

        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');
        $response->redirect('/admin/bookings');
    }

    public function activate(Request $request, Response $response)
    {
        if (!$request->isPost()) {
            $response->redirect('/admin/bookings');
            return;
        }

        $body = $request->getBody();
        $bookingId = (int)($body['booking_id'] ?? 0);
        $code = $body['checkin_code'] ?? '';

        $service = new BookingService();
        $result = $service->activateBooking($bookingId, $code, (int)($this->currentUser?->id_user ?? 0));

        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');
        $response->redirect('/admin/bookings');
    }

    public function cancel(Request $request, Response $response)
    {
        if (!$request->isPost()) {
            $response->redirect('/admin/bookings');
            return;
        }

        $body = $request->getBody();
        $bookingId = (int)($body['booking_id'] ?? 0);
        $reason = trim($body['reason'] ?? '');

        $service = new BookingService();
        $result = $service->cancelBooking($bookingId, (int)($this->currentUser?->id_user ?? 0), $reason ?: null);

        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');
        $response->redirect('/admin/bookings');
    }
}
