<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\BookingMiddleware;
use App\Core\Request;
use App\Core\Response;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Core\Services\BookingValidator;
use App\Core\Services\EmailService;
use App\Core\Services\Logger;
use App\Models\Anggota_Booking;

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
        Booking::expireStaleDrafts();
        if (!$request->isPost()) {
            $response->redirect('/rooms');
            return;
        }

        $user = $this->currentUser;
        if (!$user instanceof User) {
            $response->redirect('/login');
            return;
        }

        $body = $request->getBody();
        $roomId = (int)($body['ruangan_id'] ?? 0);
        $room = Room::findOne(['id_ruangan' => $roomId]);
        if (!$room) {
            App::$app->session->setFlash('error', 'Ruangan tidak ditemukan.');
            $response->redirect('/rooms');
            return;
        }

        $usageDate = $body['tanggal_penggunaan_ruang'];
        if (!$usageDate) {
            App::$app->session->setFlash('error', 'Tanggal penggunaan wajib diisi.');
            $response->redirect('/rooms/show?id_ruangan=' . $room->id_ruangan);
            return;
        }

        if (Booking::userHasActiveParticipation((int)$user->id_user)) {
            App::$app->session->setFlash('error', 'Anda sudah create booking/join booking yang akan datang. Selesaikan terlebih dahulu sebelum membuat yang baru.');
            $response->redirect('/dashboard');
            return;
        }

        $validation = BookingValidator::validate($body, $room, $user);
        if (!$validation['valid']) {
            App::$app->session->setFlash('error', implode("\n", $validation['errors']));
            $response->redirect('/rooms/show?id_ruangan=' . $room->id_ruangan);
            return;
        }

        $booking = new Booking();
        $booking->user_id = (int)$user->id_user;
        $booking->ruangan_id = $roomId;
        $booking->tanggal_booking = date('Y-m-d H:i:s');
        $booking->tanggal_penggunaan_ruang = $usageDate;
        $booking->waktu_mulai = $body['waktu_mulai'];
        $booking->waktu_selesai = $body['waktu_selesai'];
        $booking->tujuan = $body['tujuan'];
        $booking->status = 'draft';
        // error_log('Before save - status: ' . $booking->status);
        $booking->invite_token = $booking->invite_token ?? $this->generateInviteKode();
        
        if ($booking->save()) {

            $room = Room::findOne(['id_ruangan' => $roomId]);
            $pic = User::findOne(['id_user' => $booking->user_id]);
            $bookingDate = date('d M Y', strtotime($booking->tanggal_penggunaan_ruang));
            if ($pic instanceof User) {
                $subject = 'Created Booking Draft | Library Booking App';
                $bookingDate = date('d M Y', strtotime($booking->tanggal_penggunaan_ruang));
                $emailBody = "
                <p> Hai <strong>{$pic->nama}</strong>, </p>
                <p> Anda membuat booking di <strong>{$room->nama_ruangan}</strong> </p>
                <p> <strong> Tanggal Penggunaan: </strong> {$bookingDate} </p>
                <p> <strong> Waktu: </strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
                <p> <strong> Kami harap anda untuk segera menambahkan anggota sesuai dengan kapasitas minimum: <strong>{$room->kapasitas_min}</strong> dan kapasitas maksimum ruangan <strong>{$room->kapasitas_max}</strong> </p>
                <p> <strong> Batas akhir pengiriman draft adalah 5 menit sebelum waktu mulai </strong> </p>
                <p> <strong> Jika melewati batas akhir maka akan booking akan otomatis expired dan konsekuensi peringatan akan ditanggung oleh PIC (Orang yang membuat booking) </strong </p>

                <p> Terima kasih, <br> Library Booking App </p>
                ";

            $emailSent = EmailService::send($pic->email, $pic->nama, $subject, $emailBody);
            if (!$emailSent) {
                Logger::warning('Failed to send notification email', [
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

            Logger::booking('draft created', (int)$user->id_user, $booking->id_booking, [
                'room_id' => $roomId,
                'usage_date' => $usageDate,
                'time' => $body['waktu_mulai'] . ' - ' . $body['waktu_selesai'],
                'status' => $booking->status
            ]);

            App::$app->session->setFlash('success', 'Draft booking berhasil dibuat.');
            $response->redirect('/bookings/draft?id=' . $booking->id_booking);
            return;
        }

        Logger::error('Failed to create draft booking', [
            'user_id' => $user->id_user,
            'room_id' => $roomId
        ]);
        App::$app->session->setFlash('error', 'Gagal membuat draft booking');
        $response->redirect('/rooms/show?id=' . $booking->ruangan_id);
    }

    public function submitDraft(Request $request, Response $response) {
        Booking::expireStaleDrafts();
        $user = $this->currentUser;
        $id_booking = (int)($request->getBody()['booking_id']);
        $booking = Booking::findOne($id_booking);

        if (!$booking || $booking->status !== 'draft') {
            App::$app->session->setFlash('error', 'Draft tidak ditemukan.');
            $response->redirect('/dashboard');
            return;
        }

        $currentUserId = (int)$user->id_user;
        if (!$booking->userCanAccess($currentUserId)) {
            App::$app->session->setFlash('error', 'Anda tidak memiliki akses ke draft ini.');
            $response->redirect('/dashboard');
            return;
        }

        if (!$booking->meetsMemberMinimum()) {
            App::$app->session->setFlash('error', 'Jumlah anggota belum memenuhi syarat minimum.');
            $response->redirect('/bookings/draft?id=' . $booking->id_booking);
            return;
        }

        $maxMembers = $booking->getMaximumMembersRequired();
        $currentMembers = $maxMembers > 0 ? $booking->getMemberCount() : 0;
        if ($maxMembers > 0 && $currentMembers > $maxMembers) {
            // error_log(sprintf(
            //     'Maximum members exceeded for booking %d: %d/%d',
            //     $booking->id_booking,
            //     $currentMembers,
            //     $maxMembers
            // ));
            App::$app->session->setFlash('error', 'Jumlah anggota melebihi kapasitas maksimal');
            $response->redirect('/bookings/draft?id=' . $booking->id_booking);
            return;
        }

        $booking->status ='pending';
        $booking->save();

        $pic = User::findone(['id_user' => $booking->user_id]);
        if ($pic instanceof User) {
            $room = Room::findone(['id_ruangan' => $booking->ruangan_id]);
            $bookingDate = date('d M Y', strtotime($booking->tanggal_penggunaan_ruang));
            $subject = 'Draft sent | Library Booking App';
            $members = $booking->getMembers();
            $memberLines = array_map(function ($m) {
                return sprintf('%s (%s)', $m['nama'], $m['email']);
            }, $members);
            $memberList = implode('<br>', $memberLines);
            $emailBody = "
                <p> Hai <strong>{$pic->nama}</strong>, </p>
                <p> Draft booking kamu untuk <strong> {$room->nama_ruangan} </strong> sudah dikirim ke admin. </p>
                <p><strong>Tanggal Penggunaan:</strong> {$bookingDate}</p>
                <p><strong>Waktu:</strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
                <p> Anggota : </p>
                <p> {$memberList} </p>
                <p>Kami akan memberi tahu kamu setelah admin memberikan keputusan.</p>
                <p>Terima kasih,<br>Library Booking App</p>
            ";

            $emailSent = EmailService::send($pic->email, $pic->nama, $subject, $emailBody);
            if (!$emailSent) {
                Logger::warning('Failed to send booking submission email', [
                    'booking_id' => $booking->id_booking,
                    'user_id' => $pic->id_user,
                ]);
            }
        } else {
            Logger::warning('Booking owner missing when sending submission email', [
                'booking_id' => $booking->id_booking,
                'owner_id' => $booking->user_id,
            ]);
        }

        Logger::booking('submitted to admin', (int)$user->id_user, $id_booking);
        App::$app->session->setFlash('success', 'Booking dikirim ke admin.');
        $response->redirect('/dashboard');
    }

    public function showDraft(Request $request, Response $response) {
        Booking::expireStaleDrafts();
        $user = $this->currentUser;
        if (!$user instanceof User) {
            $response->redirect('/login');
            return;
        }
        $body = $request->getBody();
        $id_booking = (int)($body['id'] ?? $body['booking_id'] ?? 0);
        $booking = Booking::findOne($id_booking);

        if (Booking::userHasPendingFeedback((int)$user->id_user)) {
            App::$app->session->setFlash('error', 'Silakan isi feedback untuk booking sebelumnya sebelum membuat booking baru.');
            $response->redirect('/dashboard');
            return;
        }
        
        Logger::debug('showDraft accessed', [
            'user_id' => $user->id_user,
            'booking_id' => $id_booking,
            'request_body' => $body
        ]);
        
        
        if (!$booking || $booking->status !== 'draft') {
            Logger::warning('Draft booking not found or invalid', [
                'user_id' => $user->id_user,
                'booking_id' => $id_booking,
                'booking_exists' => $booking ? 'yes' : 'no',
                'booking_status' => $booking ? $booking->status : 'N/A'
            ]);
            App::$app->session->setFlash('error', 'Draft tidak tersedia');
            $response->redirect('/dashboard');
            return;
        }

        $currentUserId = (int)$user->id_user;
        if (!$booking->userCanAccess($currentUserId)) {
            App::$app->session->setFlash('error', 'Anda tidak memiliki akses ke draft ini.');
            $response->redirect('/dashboard');
            return;
        }

        $this->setLayout('main');
        $this->setTitle('Draft Booking');

        echo $this->render('User/Bookings/Draft', [
            'booking' => $booking,
            'canSubmit' => $booking->meetMemberRequirement(),
            'requiredMembers' => $booking->getMinimumMembersRequired(),
            'maximumMembers' => $booking->getMaximumMembersRequired(),
            'currentMembers' => $booking->getMemberCount(),
        ]);
    }

    public function addMember(Request $request, Response $response) {
        Booking::expireStaleDrafts();
        $user = $this->currentUser;
        if (!$request->isPost()) {
            $response->redirect('/dashboard');
            return;
        }

        $bookingId = (int)$request->getBody()['booking_id'];
        $memberEmail = trim($request->getBody()['member_email']);
        $currentUserId = (int)$user->id_user;

        $booking = Booking::findOne($bookingId);
        if (!$booking || $booking->status !== 'draft') {
            App::$app->session->setFlash('error', 'Draft tidak valid');
            $response->redirect('/dashboard');
            return;
        }

        $member = User::findOne(['email' => $memberEmail]);
        if (!$member) {
            App::$app->session->setFlash('error', 'User tidak ditemukan.');
            $response->redirect('/bookings/draft?id=' . $bookingId);
            return;
        }

        if ((int)$member->id_user === (int)$booking->user_id) {
            App::$app->session->setFlash('error', 'PIC tidak perlu ditambahkan sebagai anggota.');
            $response->redirect('/bookings/draft?id=' . $bookingId);
        }
        
        if (Booking::userHasActiveParticipation((int)$member->id_user)) {
            App::$app->session->setFlash('error', 'User tersebut sudah terlibat dalam booking lain.');
            $response->redirect('/bookings/draft?id=' . $bookingId);
            return;
        }

        if ((int)$booking->user_id !== $currentUserId) {
            App::$app->session->setFlash('error', 'Hanya PIC yang dapat menambah anggota.');
            $response->redirect('/dashboard');
            return;
        }

        $maximumMembers = $booking->getMaximumMembersRequired();
        if ($maximumMembers > 0 && $booking->getMemberCount() >= $maximumMembers) {
            App::$app->session->setFlash('error', 'Jumlah anggota sudah mencapai kapasitas maksimum.');
            $response->redirect('/bookings/draft?id=' . $bookingId);
            return;
        }

        $existing = App::$app->db->prepare("
            SELECT 1 FROM anggota_booking WHERE booking_id = :booking AND user_id = :user LIMIT 1
        ");
        $existing->bindValue(':booking', $bookingId, \PDO::PARAM_INT);
        $existing->bindValue(':user', $member->id_user, \PDO::PARAM_INT);
        $existing->execute();
        if ($existing->fetch()) {
            App::$app->session->setFlash('error', 'Anggota sudah terdaftar.');
            $response->redirect('/bookings/draft?id=' . $bookingId);
            return;
        }

        $inviteToken = $booking->invite_token ?: $this->generateInviteKode();
        $booking->invite_token = $inviteToken;
        $booking->save();
        $room = Room::findOne(['id_ruangan' => $booking->ruangan_id]);
        $pic = User::findOne(['id_user' => $booking->user_id]);
        if ($pic instanceof User) {
        $subject = 'Booking Link Invitation | Library Booking App';
        $bookingDate = date('d M Y', strtotime($booking->tanggal_penggunaan_ruang));
        $link = ($_ENV['APP_URL']) . "/bookings/join?code={$inviteToken}";
        $emailBody = "
        <p> Hai <strong> {$member->nama}</strong>, </p>
        <p> <strong>{$pic->nama} </strong> mengundang kamu untuk bergabung pada booking di <strong>{$room->nama_ruangan}</strong>. </p>
        <p> <strong>Tanggal Penggunaan: </strong> {$bookingDate} </p>
        <p><strong>Waktu:</strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
        <p> Klik link berikut untuk menerima undangan: </p>
        <p><a href=\"{$link}\">Gabung ke Booking</a></p>
        <p>Jika tombol tidak berfungsi, salin link berikut:</p>
        <p style=\"word-break: break-all;\">{$link}</p>
        <p> Terima kasih, <br> Library Booking App</p>
        ";

        $emailSent = EmailService::send($member->email, $member->nama, $subject, $emailBody);
        if (!$emailSent) {
                Logger::warning('Failed to send booking invitation email', [
                    'booking_id' => $bookingId,
                    'member_id' => $member->id_user,
                ]);
            }
        } else {
            Logger::warning('Booking owner missing when sending invite', [
                'booking_id' => $bookingId,
                'owner_id' => $booking->user_id,
            ]);
        }

        App::$app->session->setFlash('success', "Link Join dikirim ke : {$memberEmail}");
        $response->redirect('/bookings/draft?id=' . $bookingId);
    }

    private function generateInviteKode(): string
    {
        do {
            $token = strtoupper(bin2hex(random_bytes(3)));
            $exists = Booking::findOne(['invite_token' => $token]);
        } while ($exists);

        return $token;
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
        Booking::expireStaleDrafts();

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
        
        $booking = Booking::findOne(['invite_token' => $token]);
        if (!$booking || $booking->status !== 'draft') {
        App::$app->session->setFlash('error', 'Link tidak valid.');
        $response->redirect('/bookings/join');
        return;
        }

        if ($booking->user_id === (int)$user->id_user) {
            App::$app->session->setFlash('error', 'Anda adalah pemilik booking ini.');
            $response->redirect('/bookings/join');
            return;
        }

        $alreadyMember = App::$app->db->prepare("
            SELECT 1 FROM anggota_booking where booking_id = :booking AND user_id = :user LIMIT 1
        ");
        $alreadyMember->bindValue(':booking', $booking->id_booking, \PDO::PARAM_INT);
        $alreadyMember->bindValue(':user', $user->id_user, \PDO::PARAM_INT);
        $alreadyMember->execute();

        if ($alreadyMember->fetch()) {
            App::$app->session->setFlash('info', 'Anda sudah tergabung.');
            $response->redirect('/dashboard');
            return;
        }

        $member = new Anggota_Booking();
        $member->booking_id = $booking->id_booking;
        $member->user_id = $user->id_user;
        $member->save();

        App::$app->session->setFlash('success', 'Berhasil bergabung ke draft booking.');
        $response->redirect('/bookings/draft?id=' . $booking->id_booking);
    }
}
