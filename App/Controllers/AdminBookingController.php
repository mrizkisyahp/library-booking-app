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

        $pic = User::findOne(['id_user' => $booking->user_id]);
        if ($pic instanceof User) {
            $room = Room::findOne(['id_ruangan' => $booking->ruangan_id]);
            $subject = 'Booking Draft Approved | Library Booking App';
            $bookingDate = date('d M Y', strtotime($booking->tanggal_penggunaan_ruang));
            $emailBody = "
            <p>Hai <strong>{$pic->nama}</strong>, </p>
            <p>Pengajuan booking ruangan kamu {$room->nama_ruangan} telah disetujui oleh admin. </p>
            <p><strong>Tanggal Penggunaan:</strong> {$bookingDate}</p>
            <p><strong>Waktu:</strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
            <p><strong>Kode Check-in:</strong> {$booking->checkin_code}</p>
            <p>Harap lakukan check-in sebelum waktu mulai.</p>
            <p><strong>Kode Check-in ini digunakan untuk mengambil kunci ruangan dari admin. Harap menyebutkan kode check-in agar admin bisa mengvalidasi.</strong></p>
            <p>Terima kasih, <br> Library Booking App PNJ </p>
            ";

        $emailSent = EmailService::send($pic->email, $pic->nama, $subject, $emailBody);
        if (!$emailSent) {
            Logger::warning('Failed to send booking approval email', [
                'booking_id' => $booking->id_booking,
                'user_id' => $pic->id_user,
            ]);
        }
        } else {
            Logger::warning('Booking Owner not found while sending approval email', [
                'booking_id' => $booking->id_booking,
                'user_id' => $booking->user_id,
            ]);
        }

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

        $pic = User::findOne(['id_user' => $booking->user_id]);
        if ($pic instanceof User) {
            $room = Room::findOne(['id_ruangan' => $booking->ruangan_id]);
            $subject = 'Booking Selesai - Mohon Feedback Anda | Library Booking App';
            $bookingDate = date('d M Y', strtotime($booking->tanggal_penggunaan_ruang));

            $emailBody = "
            <p>Hai <strong>{$pic->nama}</strong>,</p>
            <p>Terima kasih sudah menggunakan ruangan <strong>{$room->nama_ruangan}</strong>. Booking kamu pada <strong>{BOOKING_DATE}</strong> pukul <strong>{START_TIME} - {END_TIME}</strong> sudah ditandai selesai.</p>
            <p>Kami ingin mendengar pengalaman kamu. Silakan isi feedback melalui tautan berikut:</p>
            <p>Masukan kamu membantu kami meningkatkan layanan.</p>
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

        Logger::admin('completed booking', (int)$admin->id_user, "Booking #{$id_booking} marked as completed");
        App::$app->session->setFlash('success', 'Booking selesai.');
        $response->redirect('/admin/bookings');
    }

    private function generateCheckinCode(): string
    {
        do {
            $code = strtoupper(bin2hex(random_bytes(3)));
            $exists = Booking::findOne(['checkin_code' => $code]);
        } while ($exists);

        return $code;
    }
}
