<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Middleware\AdminMiddleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Services\Logger;
use App\Core\Services\EmailService;
use App\Models\Room;
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

        $pic = User::findOne(['id_user' => $booking->user_id]);
        if ($pic instanceof User) {
            $room = Room::findOne(['id_ruangan' => $booking->ruangan_id]);
            $subject = 'Check-in Berhasil | Library Booking App';
            $bookingDate = date('d M Y', strtotime($booking->tanggal_penggunaan_ruang));

            $emailBody = "
            <p> Hai <strong>{$pic->nama}</strong>, </p>
            <p> Check-in untuk booking ruangan <strong>{$room->nama_ruangan}</strong> telah berhasil divalidasi Admin. </p>
            <p><strong>Tanggal Penggunaan:</strong> {$bookingDate}</p>
            <p><strong>Waktu:</strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
            <p> <strong> Harap kunci ruangan dijaga jangan sampai hilang </strong> </p>
            <p> <strong> Silahkan gunakan ruangan sesuai dengan tujuannya dan jaga fasilitas selama pemakaian. </strong> </p>
            <p>Terima kasih,<br>Library Booking App</p>
            ";
        }
        $emailSent = EmailService::send($pic->email, $pic->nama, $subject, $emailBody);
        if (!$emailSent) {
            Logger::warning('Failed to send booking approval email', [
                'booking_id' => $booking->id_booking,
                'user_id' => $pic->id_user,
            ]);
        } else {
            Logger::warning('Booking Owner not found while sending approval email', [
                'booking_id' => $booking->id_booking,
                'user_id' => $booking->user_id,
            ]);
        }
    
        $admin = $this->currentUser;
        Logger::admin('checked in booking', (int)$admin->id_user, "Booking #{$booking->id_booking} checked in with code: {$code}");
        App::$app->session->setFlash('success', 'Booking ditandai sebagai sedang berjalan.');
        $response->redirect('/admin/bookings');
    }
}
