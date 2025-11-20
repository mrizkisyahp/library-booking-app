<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\BookingMiddleware;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Core\Services\UserBookingService;

class UserBookingController extends Controller {
    protected ?User $currentUser = null;
    public function __construct() {
        $this->registerMiddleware(new AuthMiddleware());
        $this->currentUser = App::$app->user instanceof User ? App::$app->user : null;

        $this->registerMiddleware(new BookingMiddleware([
            'showDraft',
            'submitDraft',
            'addMember',
        ]));
    }

    public function createDraft(Request $request, Response $response) {
        // Booking::expireStaleDrafts();
        if (!$request->isPost()) {
            $response->redirect('/rooms');
            return;
        }

        $user = $this->currentUser;
        if (!$user instanceof User) {
            $response->redirect('/login');
            return;
        }

        $bookingService = new UserBookingService();
        $result = $bookingService->createDraft($user, $request->getBody());

        // $debug = $result;
        
        // echo '<pre>';
        // print_r($debug);
        // echo '</pre>';
        // exit;
        if ($result['success'] ?? false) {
            App::$app->session->setFlash('success', $result['message'] ?? 'Draft booking berhasil dibuat.');
            $response->redirect($result['redirect'] ?? '/dashboard');
            return;
        }

        App::$app->session->setFlash('error', $result['message'] ?? 'Gagal membuat draft booking');
        $response->redirect($result['redirect'] ?? '/rooms');
    }

    public function submitDraft(Request $request, Response $response) {
        // Booking::expireStaleDrafts();
        $user = $this->currentUser;
        $bookingId = (int)($request->getBody()['booking_id']);
        $bookingService = new UserBookingService();
        $result = $bookingService->submitDraft($bookingId, (int)$user->id_user);

        if ($result['success'] ?? false) {
            App::$app->session->setFlash('success', $result['message'] ?? 'Booking dikirim ke admin.');
            $response->redirect($result['redirect'] ?? '/dashboard');
            return;
        }

        App::$app->session->setFlash('error', $result['message'] ?? 'Gagal mengirim draft booking.');
        $redirect = $result['redirect'] ?? '/dashboard';
        $response->redirect($redirect);
    }

    public function showDraft(Request $request, Response $response) {
        // Booking::expireStaleDrafts();
        $user = $this->currentUser;
        if (!$user instanceof User) {
            $response->redirect('/login');
            return;
        }
        $body = $request->getBody();
        $bookingId = (int)($body['id'] ?? $body['booking_id'] ?? 0);

        $bookingService = new UserBookingService();
        $result = $bookingService->getDraftViewData((int)$user->id_user, $bookingId);

        if (!$result['success']) {
            App::$app->session->setFlash('error', $result['message'] ?? 'Draft tidak tersedia');
            $response->redirect('/dashboard');
            return;
        }

        $data = $result['data'] ?? [];

        $this->setLayout('main');
        $this->setTitle('Draft Booking');

        return $this->render('User/Bookings/Draft', $data);
    }

    public function addMember(Request $request, Response $response) {
        // Booking::expireStaleDrafts();
        $user = $this->currentUser;
        if (!$request->isPost()) {
            $response->redirect('/dashboard');
            return;
        }

        $bookingId = (int)$request->getBody()['booking_id'];
        $memberEmail = trim($request->getBody()['member_email']);
        $bookingService = new UserBookingService();
        $result = $bookingService->addMember($bookingId, (int)$user->id_user, $memberEmail);

        if ($result['success'] ?? false) {
            App::$app->session->setFlash('success', $result['message'] ?? 'Anggota ditambahkan.');
            $response->redirect($result['redirect'] ?? '/bookings/draft?id=' . $bookingId);
            return;
        }

        App::$app->session->setFlash('error', $result['message'] ?? 'Gagal menambah anggota.');
        if ($result['fatal'] ?? false) {
            $response->redirect('/dashboard');
            return;
        }

        $response->redirect('/bookings/draft?id=' . $bookingId);
    }

    public function showJoinForm(Request $request, Response $response)
    {
        $this->setLayout('main');
        $this->setTitle('Gabung Booking');

        return $this->render('User/Bookings/Join', [
            'prefill' => $request->getBody()['code'] ?? null,
        ]);
    }

    public function joinByLink(Request $request, Response $response) {
        // Booking::expireStaleDrafts();

        $user = $this->currentUser;
        if (!$user instanceof User) {
            $response->redirect('/login');
            return;
        }

        $body = $request->getBody();
        $token = trim((string)$body['invite_token'] ?? '');
        if ($token === '') {
            App::$app->session->setFlash('error', 'Link tidak boleh kosong');
            $response->redirect('/bookings/join');
            return;
        }
        
        $bookingService = new UserBookingService();
        $result = $bookingService->joinViaInviteToken((int)$user->id_user, $token);

        App::$app->session->setFlash($result['success'] ? 'success' : (($result['info'] ?? false) ? 'info' : 'error'), $result['message'] ?? '');

        if ($result['success'] ?? false) {
            $response->redirect($result['redirect'] ?? '/dashboard');
            return;
        }

        if ($result['info'] ?? false) {
            $response->redirect('/dashboard');
            return;
        }

        $response->redirect('/bookings/join');
    }

    public function showMyBooking(Request $request, Response $response) {
        $user = $this->currentUser;
        if (!$user instanceof User) {
            $response->redirect('/login');
            return;
        }

        $this->setLayout('main');
        $this->setTitle('My Bookings | Library Booking App');

        return $this->render('User/Bookings/Index');
    }
}
