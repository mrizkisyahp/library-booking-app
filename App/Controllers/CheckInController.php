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

class CheckInController extends Controller
{
    protected ?User $currentUser = null;
    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware());
        $this->currentUser = App::$app->user instanceof User ? App::$app->user : null;
    }

    public function index()
    {
        $this->setLayout('main');
        $this->setTitle('Check-in Booking | Library Booking App');

        return $this->render('Admin/Bookings/CheckIn');
    }

    public function verify(Request $request, Response $response)
    {
        if (!$request->isPost()) {
            $response->redirect('/checkin');
            return;
        }

        $code = strtoupper(trim($request->getBody()['checkin_code'] ?? ''));
        if ($code === '') {
            App::$app->session->setFlash('error', 'Kode tidak boleh kosong.');
            $response->redirect('/checkin');
            return;
        }

        $booking = Booking::findOne(['checkin_code' => $code]);

        if (!$booking || $booking->status !== 'verified') {
            App::$app->session->setFlash('error', 'Kode tidak ditemukan atau booking belum diverifikasi.');
            $response->redirect('/checkin');
            return;
        }

        $booking->status = 'active';
        $booking->save();

        $admin = $this->currentUser;
        Logger::admin('checked in booking', (int)$admin->id_user, "Booking #{$booking->id_booking} checked in with code: {$code}");
        App::$app->session->setFlash('success', 'Booking ditandai sebagai sedang berjalan.');
        $response->redirect('/admin/bookings');
    }
}
