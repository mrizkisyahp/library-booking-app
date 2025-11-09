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
            App::$app->session->setFlash('error', implode(' ', $validation['errors']));
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
        $booking->invite_token = $booking->invite_token ?? $this->generateInviteToken();
        
        if ($booking->save()) {
            Logger::booking('draft created', (int)$user->id_user, $booking->id_booking, [
                'room_id' => $roomId,
                'usage_date' => $usageDate,
                'time' => $body['waktu_mulai'] . ' - ' . $body['waktu_selesai']
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

        if ($booking->meetsMemberMaximum()) {
            App::$app->session->setFlash('error', 'Jumlah anggota melebihi kapaistas maksimal');
            $response->redirect('/bookings/draft?id=' . $booking->id_booking);
            return;
        }

        $booking->status ='pending';
        $booking->save();

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

        if ($booking->meetsMemberMaximum()) {
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

        $anggota = new Anggota_Booking();
        $anggota->booking_id = $bookingId;
        $anggota->user_id = $member->id_user;
        $anggota->save();

        Logger::booking('member added', $booking->user_id, $bookingId, [
            'member_email' => $memberEmail,
            'member_id' => $member->id_user
        ]);
        App::$app->session->setFlash('success', 'Anggota ditambahkan.');
        $response->redirect('/bookings/draft?id=' . $bookingId);
    }

    private function generateInviteToken(): string
    {
        do {
            $token = bin2hex(random_bytes(16));
            $exists = Booking::findOne(['invite_token' => $token]);
        } while ($exists);

        return $token;
    }

    public function showJoinForm(Request $request, Response $response)
    {
        $this->setLayout('main');
        $this->setTitle('Gabung Booking');

        return $this->render('User/Bookings/Join', [
            'prefill' => $request->getBody()['token'] ?? null,
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