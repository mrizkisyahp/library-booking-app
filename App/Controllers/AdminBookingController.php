<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Middleware\AdminMiddleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Services\AdminBookingService;
use App\Models\Booking;
use App\Models\User;

class AdminBookingController extends Controller {
    protected ?User $currentUser = null;
    public function __construct() {
        $this->registerMiddleware(new AdminMiddleware());
        $this->currentUser = App::$app->user instanceof User ? App::$app->user : null;
    }

    public function index() {
        $this->setLayout('main');
        $this->setTitle('Manajemen Booking | Library Booking App');

        $query = App::$app->request->getBody();
        $filters = [
            'keyword' => $query['keyword'] ?? null,
            'status' => $query['status'] ?? null,
            'page' => (int)($query['page'] ?? ($_GET['page'] ?? 1)),
        ];

        $service = new AdminBookingService();
        $result = $service->listAllBookings($filters);
        $data = $result['data'] ?? [];

        return $this->render('Admin/Bookings/Index', [
            'bookings' => $data['bookings'] ?? [],
            'filters' => $filters,
            'statusOptions' => $data['statusOptions'] ?? $service->getStatusOptions(),
            'totalBookings' => $data['total'] ?? 0,
            'currentPage' => $data['currentPage'] ?? $filters['page'],
            'perPage' => $data['perPage'] ?? 20,
        ]);
    }

    public function create() {
        $this->setLayout('main');
        $this->setTitle('Create Booking | Library Booking App');

        $service = new AdminBookingService();

        $options = $service->getFormSelections();

        return $this->render('Admin/Bookings/Create', [
            'booking' => new Booking(),
            'rooms' => $options['rooms'] ?? [],
            'users' => $options['users'] ?? [],
            'errors' => [],
            'old' => [],
        ]);
    }

    public function store(Request $request, Response $response) {
        if (!$request->isPost() || !Csrf::validateToken($request->getBody()['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'CSRF token tidak valid.');
            $response->redirect('/admin/bookings');
            return;
        }

        $service = new AdminBookingService();
        $result = $service->createBooking($request->getBody());
        if (!$result['success']) {
            error_log(print_r($result['data']['errors'] ?? [], true));
        }

        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');
        $response->redirect('/admin/bookings');

        if ($result['success']) {
            $response->redirect('/admin/bookings');
            return;
        }

        $options = $service->getFormSelections();
        $this->setLayout('main');
        $this->setTitle('Create Booking | Library Booking App');

        return $this->render('Admin/Bookings/Create', [
            'booking' => new Booking(),
            'rooms' => $options['rooms'] ?? [],
            'users' => $options['users'] ?? [],
            'errors' => $result['errors'] ?? [],
            'old' => $result['data']['payload'] ?? $request->getBody(),
        ]);
    }

    public function edit(Request $request, Response $response) {
        $this->setLayout('main');
        $this->setTitle('Edit Booking | Library Booking App');

        $bookingId = (int)($request->getBody()['id_booking'] ?? $request->getBody()['id'] ?? 0);
        $service = new AdminBookingService();
        $booking = $service->getBookingById($bookingId);

        if (!$booking) {
            App::$app->session->setFlash('error', 'Booking tidak ditemukan.');
            $response->redirect('/admin/bookings');
            return;
        }

        $options = $service->getFormSelections();
        return $this->render('Admin/Bookings/Edit', [
            'booking' => $booking,
            'rooms' => $options['rooms'] ?? [],
            'users' => $options['users'] ?? [],
            'statusOptions' => $options['statusOptions'] ?? [],
            'errors' => [],
            'old' => [],
        ]);
    }

    public function update(Request $request, Response $response) {
        if (!$request->isPost() || !Csrf::validateToken($request->getBody()['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'CSRF token tidak valid.');
            $response->redirect('/admin/bookings');
            return;
        }

        $bookingId = (int)($request->getBody()['id_booking'] ?? $request->getBody()['id'] ?? 0);
        $service = new AdminBookingService();
        $result = $service->updateBooking($bookingId, $request->getBody());

        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');

        if ($result['success']) {
            $response->redirect('/admin/bookings');
            return;
        }

        $booking = $service->getBookingById($bookingId);
        $options = $service->getFormSelections();
        $this->setLayout('main');
        $this->setTitle('Edit Booking | Library Booking App');

        return $this->render('Admin/Bookings/Edit', [
            'booking' => $booking,
            'rooms' => $options['rooms'] ?? [],
            'users' => $options['users'] ?? [],
            'statusOptions' => $options['statusOptions'] ?? [],
            'errors' => $result['data']['errors'] ?? [],
            'old' => $result['data']['payload'] ?? $request->getBody(),
        ]);
    }

    public function delete(Request $request, Response $response) {
        if (!$request->isPost() || !Csrf::validateToken($request->getBody()['csrf_token'] ?? '')) {
            App::$app->session->setFlash('error', 'CSRF token tidak valid.');
            $response->redirect('/admin/bookings');
            return;
        }

        $bookingId = (int)($request->getBody()['id_booking'] ?? $request->getBody()['id'] ?? 0);
        $service = new AdminBookingService();
        $result = $service->deleteBooking($bookingId);

        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');
        $response->redirect('/admin/bookings');
    }

    public function detail(Request $request, Response $response)
    {
        $bookingId = (int)($request->getBody()['id'] ?? $request->getBody()['id_booking'] ?? 0);
        if ($bookingId <= 0) {
            App::$app->session->setFlash('error', 'ID booking tidak valid.');
            $response->redirect('/admin/bookings');
            return;
        }

        $service = new AdminBookingService();
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

        $service = new AdminBookingService();
        $result = $service->verifyBooking($id_booking, (int)$admin->id_user);

        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');
        $response->redirect('/admin/bookings');
    }

    public function complete(Request $request, Response $response) {
        $admin = $this->currentUser;
        $id_booking = (int)($request->getBody()['booking_id']);

        $service = new AdminBookingService();
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

        $service = new AdminBookingService();
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

        $service = new AdminBookingService();
        $result = $service->cancelBooking($bookingId, (int)($this->currentUser?->id_user ?? 0), $reason ?: null);

        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');
        $response->redirect('/admin/bookings');
    }
}
