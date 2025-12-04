<?php

namespace App\Core\Services;

use App\Core\App;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Models\Anggota_Booking;
use App\Core\Services\BookingValidator;
use App\Core\Services\EmailService;
use App\Core\Services\Logger;
use App\Core\Services\FeedbackService;

class UserBookingService
{
    private const PER_PAGE = 20;
    public function createDraft(User $user, array $data): array
    {
        $roomId = (int) ($data['ruangan_id'] ?? 0);
        $room = Room::Query()->where('id_ruangan', $roomId)->first();
        if (!$room) {
            return [
                'success' => false,
                'message' => 'Ruangan tidak ditemukan.',
                'redirect' => '/rooms',
            ];
        }

        $usageDate = $data['tanggal_penggunaan_ruang'] ?? '';
        if (!$usageDate) {
            return [
                'success' => false,
                'message' => 'Tanggal penggunaan wajib diisi.',
                'redirect' => '/rooms/show?id_ruangan=' . $room->id_ruangan,
            ];
        }

        if ($this->userHasActiveParticipation((int) $user->id_user)) {
            return [
                'success' => false,
                'message' => 'Anda sudah create booking/join booking yang akan datang. Selesaikan terlebih dahulu sebelum membuat yang baru.',
                'redirect' => '/rooms',
            ];
        }

        $feedbackService = new FeedbackService();
        if ($feedbackService->userHasPendingFeedback((int) $user->id_user)) {
            return [
                'success' => false,
                'message' => 'Silakan isi feedback untuk booking sebelumnya sebelum membuat booking baru.',
                'redirect' => '/rooms',
            ];
        }

        $validation = BookingValidator::validate($data, $room, $user);
        if (!($validation['valid'] ?? false)) {
            $message = implode("\n", $validation['errors'] ?? []);
            return [
                'success' => false,
                'message' => $message,
                'redirect' => '/rooms/show?id_ruangan=' . $room->id_ruangan,
            ];
        }

        $booking = new Booking();
        $booking->user_id = (int) $user->id_user;
        $booking->ruangan_id = $roomId;
        $booking->tanggal_booking = date('Y-m-d H:i:s');
        $booking->tanggal_penggunaan_ruang = $usageDate;
        $booking->waktu_mulai = $data['waktu_mulai'] ?? '';
        $booking->waktu_selesai = $data['waktu_selesai'] ?? '';
        $booking->tujuan = $data['tujuan'] ?? '';
        $booking->status = 'draft';
        $booking->invite_token = $booking->invite_token ?? $this->generateInviteToken();

        if (!$booking->save()) {
            Logger::error('Failed to create draft booking', [
                'user_id' => $user->id_user,
                'room_id' => $roomId,
            ]);
            return [
                'success' => false,
                'message' => 'Gagal membuat draft booking',
                'redirect' => '/rooms/show?id=' . $roomId,
            ];
        }

        $pic = User::Query()->where('id_user', $booking->user_id)->first();
        if ($pic instanceof User) {
            $roomInfo = Room::Query()->where('id_ruangan', $booking->ruangan_id)->first();
            $bookingDate = date('d M Y', strtotime($booking->tanggal_penggunaan_ruang));
            $subject = 'Created Booking Draft | Library Booking App';
            $emailBody = "
                <p> Hai <strong>{$pic->nama}</strong>, </p>
                <p> Anda membuat booking di <strong>{$roomInfo->nama_ruangan}</strong> </p>
                <p> <strong> Tanggal Penggunaan: </strong> {$bookingDate} </p>
                <p> <strong> Waktu: </strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
                <p> <strong> Kami harap anda untuk segera menambahkan anggota sesuai dengan kapasitas minimum: <strong>{$roomInfo->kapasitas_min}</strong> dan kapasitas maksimum ruangan <strong>{$roomInfo->kapasitas_max}</strong> </p>
                <p> <strong> Batas akhir pengiriman draft adalah 5 menit sebelum waktu mulai </strong> </p>
                <p> <strong> Jika melewati batas akhir maka akan booking akan otomatis expired dan konsekuensi peringatan akan ditanggung oleh PIC (Orang yang membuat booking) </strong </p>

                <p> Terima kasih, <br> Library Booking App </p>
                ";

            if (!EmailService::send($pic->email, $pic->nama, $subject, $emailBody)) {
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

        Logger::booking('draft created', (int) $user->id_user, $booking->id_booking, [
            'room_id' => $roomId,
            'usage_date' => $usageDate,
            'time' => ($data['waktu_mulai'] ?? '') . ' - ' . ($data['waktu_selesai'] ?? ''),
            'status' => $booking->status,
        ]);

        return [
            'success' => true,
            'booking' => $booking,
            'message' => 'Draft booking berhasil dibuat.',
            'redirect' => '/bookings/draft?id=' . $booking->id_booking,
        ];
    }

    private function generateInviteToken(): string
    {
        do {
            $token = strtoupper(bin2hex(random_bytes(3)));
            $exists = Booking::Query()->where('invite_token', $token)->first();
        } while ($exists);

        return $token;
    }

    public function submitDraft(int $bookingId, int $currentUserId): array
    {
        $booking = Booking::Query()->where('id_booking', $bookingId)->first();

        if (!$booking || $booking->status !== 'draft') {
            return [
                'success' => false,
                'message' => 'Draft tidak ditemukan.',
                'redirect' => '/dashboard',
            ];
        }

        if (!$this->userCanAccessBooking($booking, $currentUserId)) {
            return [
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke draft ini.',
                'redirect' => '/dashboard',
            ];
        }

        if (!$this->meetsMemberMinimum($booking)) {
            return [
                'success' => false,
                'message' => 'Jumlah anggota belum memenuhi syarat minimum.',
                'redirect' => '/bookings/draft?id=' . $booking->id_booking,
            ];
        }

        $maxMembers = $this->getMaximumMembersRequired($booking);
        $currentMembers = $maxMembers > 0 ? $this->getMemberCount($booking) : 0;
        if ($maxMembers > 0 && $currentMembers > $maxMembers) {
            return [
                'success' => false,
                'message' => 'Jumlah anggota melebihi kapasitas maksimal',
                'redirect' => '/bookings/draft?id=' . $booking->id_booking,
            ];
        }

        $booking->status = 'pending';
        if (!$booking->save()) {
            Logger::error('Failed to submit draft booking', [
                'booking_id' => $bookingId,
                'user_id' => $currentUserId,
            ]);
            return [
                'success' => false,
                'message' => 'Gagal mengirim draft booking.',
                'redirect' => '/bookings/draft?id=' . $booking->id_booking,
            ];
        }

        Logger::booking('submitted to admin', $currentUserId, $bookingId);

        return [
            'success' => true,
            'message' => 'Booking dikirim ke admin.',
            'redirect' => '/dashboard',
        ];
    }

    public function addMember(int $bookingId, int $currentUserId, string $memberEmail): array
    {
        $booking = Booking::Query()->where('id_booking', $bookingId)->first();
        if (!$booking || $booking->status !== 'draft') {
            return [
                'success' => false,
                'fatal' => true,
                'message' => 'Draft tidak valid',
            ];
        }

        $member = User::Query()->where('email', $memberEmail)->first();
        if (!$member) {
            return [
                'success' => false,
                'fatal' => false,
                'message' => 'User tidak ditemukan.',
            ];
        }

        if ((int) $member->id_user === (int) $booking->user_id) {
            return [
                'success' => false,
                'fatal' => false,
                'message' => 'PIC tidak perlu ditambahkan sebagai anggota.',
            ];
        }

        if ($this->userHasActiveParticipation((int) $member->id_user)) {
            return [
                'success' => false,
                'fatal' => false,
                'message' => 'User tersebut sudah terlibat dalam booking lain.',
            ];
        }

        if ((int) $booking->user_id !== $currentUserId) {
            return [
                'success' => false,
                'fatal' => true,
                'message' => 'Hanya PIC yang dapat menambah anggota.',
            ];
        }

        $maximumMembers = $this->getMaximumMembersRequired($booking);
        if ($maximumMembers > 0 && $this->getMemberCount($booking) >= $maximumMembers) {
            return [
                'success' => false,
                'fatal' => false,
                'message' => 'Jumlah anggota sudah mencapai kapasitas maksimum.',
            ];
        }

        $existing = App::$app->db->prepare("
            SELECT 1 FROM anggota_booking WHERE booking_id = :booking AND user_id = :user LIMIT 1
        ");
        $existing->bindValue(':booking', $bookingId, \PDO::PARAM_INT);
        $existing->bindValue(':user', $member->id_user, \PDO::PARAM_INT);
        $existing->execute();
        if ($existing->fetch()) {
            return [
                'success' => false,
                'fatal' => false,
                'message' => 'Anggota sudah terdaftar.',
            ];
        }

        $inviteToken = $booking->invite_token ?: $this->generateInviteToken();
        $booking->invite_token = $inviteToken;
        $booking->save();

        $memberLink = ($_ENV['APP_URL']) . "/bookings/join?code={$inviteToken}";
        $room = Room::Query()->where('id_ruangan', $booking->ruangan_id)->first();
        $pic = User::Query()->where('id_user', $booking->user_id)->first();

        if ($pic instanceof User) {
            $subject = 'Booking Link Invitation | Library Booking App';
            $bookingDate = date('d M Y', strtotime($booking->tanggal_penggunaan_ruang));
            $emailBody = "
        <p> Hai <strong> {$member->nama}</strong>, </p>
        <p> <strong>{$pic->nama} </strong> mengundang kamu untuk bergabung pada booking di <strong>{$room->nama_ruangan}</strong>. </p>
        <p> <strong>Tanggal Penggunaan: </strong> {$bookingDate} </p>
        <p><strong>Waktu:</strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
        <p> Klik link berikut untuk menerima undangan: </p>
        <p><a href=\"{$memberLink}\">Gabung ke Booking</a></p>
        <p>Jika tombol tidak berfungsi, salin link berikut:</p>
        <p style=\"word-break: break-all;\">{$memberLink}</p>
        <p> Terima kasih, <br> Library Booking App</p>
        ";

            if (!EmailService::send($member->email, $member->nama, $subject, $emailBody)) {
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

        $anggota = new Anggota_Booking();
        $anggota->booking_id = $bookingId;
        $anggota->user_id = $member->id_user;
        $anggota->save();

        return [
            'success' => true,
            'message' => "Link Join dikirim ke : {$memberEmail}",
            'redirect' => '/bookings/draft?id=' . $bookingId,
        ];
    }

    public function joinViaInviteToken(int $userId, string $token): array
    {
        $token = trim($token);
        if ($token === '') {
            return [
                'success' => false,
                'fatal' => false,
                'message' => 'Link tidak boleh kosong',
            ];
        }

        $booking = Booking::Query()->where('invite_token', $token)->first();
        if (!$booking || $booking->status !== 'draft') {
            return [
                'success' => false,
                'fatal' => false,
                'message' => 'Link tidak valid.',
            ];
        }

        if ((int) $booking->user_id === $userId) {
            return [
                'success' => false,
                'fatal' => false,
                'message' => 'Anda adalah pemilik booking ini.',
            ];
        }

        $user = User::Query()->where('id_user', $userId)->first();
        if (!$user || $user->status !== 'active') {
            return [
                'success' => false,
                'fatal' => false,
                'message' => 'Wajib verifikasi kubaca terlebih dahulu.',
            ];
        }

        $alreadyMember = App::$app->db->prepare("
            SELECT 1 FROM anggota_booking WHERE booking_id = :booking AND user_id = :user LIMIT 1
        ");
        $alreadyMember->bindValue(':booking', $booking->id_booking, \PDO::PARAM_INT);
        $alreadyMember->bindValue(':user', $userId, \PDO::PARAM_INT);
        $alreadyMember->execute();
        if ($alreadyMember->fetch()) {
            return [
                'success' => false,
                'fatal' => false,
                'info' => true,
                'message' => 'Anda sudah tergabung.',
            ];
        }

        $member = new Anggota_Booking();
        $member->booking_id = $booking->id_booking;
        $member->user_id = $userId;
        $member->save();

        return [
            'success' => true,
            'message' => 'Berhasil bergabung ke draft booking.',
            'redirect' => '/bookings/draft?id=' . $booking->id_booking,
        ];
    }

    public function getDraftViewData(int $currentUserId, int $bookingId): array
    {
        $booking = Booking::Query()->where('id_booking', $bookingId)->first();

        if (!$booking || $booking->status !== 'draft') {
            return [
                'success' => false,
                'message' => 'Draft tidak tersedia',
            ];
        }

        if (!$this->userCanAccessBooking($booking, $currentUserId)) {
            return [
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke draft ini.',
            ];
        }

        $room = Room::Query()->where('id_ruangan', $booking->ruangan_id)->first();

        return [
            'success' => true,
            'message' => null,
            'data' => [
                'booking' => $booking,
                'room' => $room,
                'canSubmit' => $this->meetsMemberRequirement($booking),
                'requiredMembers' => $this->getMinimumMembersRequired($booking),
                'maximumMembers' => $this->getMaximumMembersRequired($booking),
                'currentMembers' => $this->getMemberCount($booking),
            ],
        ];
    }

    public function userCanAccessBooking(Booking $booking, int $userId): bool
    {
        if ((int) $booking->user_id === $userId) {
            return true;
        }

        $stmt = App::$app->db->prepare("
            SELECT 1 FROM anggota_booking WHERE booking_id = :booking AND user_id = :user LIMIT 1
        ");
        $stmt->bindValue(':booking', $booking->id_booking, \PDO::PARAM_INT);
        $stmt->bindValue(':user', $userId, \PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    public function userHasActiveParticipation(int $userId): bool
    {
        $stmt = App::$app->db->prepare("
            SELECT COUNT(*) FROM booking b
            LEFT JOIN anggota_booking ab ON ab.booking_id = b.id_booking AND ab.user_id = :user
            WHERE (b.user_id = :user OR ab.user_id = :user)
              AND b.status IN ('draft','pending','verified','active')
        ");
        $stmt->bindValue(':user', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    public function getMinimumMembersRequired(Booking $booking): int
    {
        $room = Room::Query()->where('id_ruangan', $booking->ruangan_id)->first();
        return $room && $room->kapasitas_min ? (int) $room->kapasitas_min : 0;
    }

    public function getMaximumMembersRequired(Booking $booking): int
    {
        $room = Room::Query()->where('id_ruangan', $booking->ruangan_id)->first();
        return $room && $room->kapasitas_max ? (int) $room->kapasitas_max : 0;
    }

    public function getMemberCount(Booking $booking): int
    {
        $db = App::$app->db;

        $membersStmt = $db->prepare("
            SELECT COUNT(*) FROM anggota_booking WHERE booking_id = :id
        ");
        $membersStmt->bindValue(':id', $booking->id_booking, \PDO::PARAM_INT);
        $membersStmt->execute();
        $members = (int) $membersStmt->fetchColumn();

        $picStmt = $db->prepare("
            SELECT 1 FROM anggota_booking WHERE booking_id = :id AND user_id = :pic LIMIT 1
        ");
        $picStmt->bindValue(':id', $booking->id_booking, \PDO::PARAM_INT);
        $picStmt->bindValue(':pic', $booking->user_id, \PDO::PARAM_INT);
        $picStmt->execute();
        $picAlreadyCounted = (bool) $picStmt->fetchColumn();

        return $picAlreadyCounted ? $members : $members + 1;
    }

    public function meetsMemberMinimum(Booking $booking): bool
    {
        $required = $this->getMinimumMembersRequired($booking);
        if ($required <= 0) {
            return true;
        }

        return $this->getMemberCount($booking) >= $required;
    }

    public function meetsMemberRequirement(Booking $booking): bool
    {
        $minRequired = $this->getMinimumMembersRequired($booking);
        $maxRequired = $this->getMaximumMembersRequired($booking);
        $currentCount = $this->getMemberCount($booking);

        if ($minRequired <= 0 && $maxRequired <= 0) {
            return true;
        }

        $meetsMin = $minRequired <= 0 || $currentCount >= $minRequired;
        $meetsMax = $maxRequired <= 0 || $currentCount <= $maxRequired;

        return $meetsMin && $meetsMax;
    }

    public function getMyBookings(int $userid, array $filters = []): array
    {

        $booking = Booking::Query()->where('user_id', $userid)->first();

        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = (int) ($filters['perPage'] ?? self::PER_PAGE);

        $queryFilters = [
            'keyword' => $filters['keyword'] ?? null,
            'tanggal_penggunaan_ruang' => $filters['tanggal_penggunaan_ruang'] ?? null,
            'status' => $filters['status'] ?? null,
        ];

        $bookings = Booking::findPaginatedMyBooking($userid, $page, $perPage, $queryFilters) ?: [];

        return [
            'success' => true,
            'data' => [
                'mybooking' => $bookings,
                'filters' => $queryFilters,
                'currentPage' => $page,
                'perPage' => $perPage,
                'total' => Booking::count($queryFilters),
            ]
        ];
    }
    public function getUserBookingDetail(int $userId, int $bookingId): array
    {
        $booking = Booking::Query()->where('id_booking', $bookingId)->first();

        if (!$booking) {
            return [
                'success' => false,
                'message' => 'Booking tidak ditemukan.',
            ];
        }

        if (!$this->userCanAccessBooking($booking, $userId)) {
            return [
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke booking ini.',
            ];
        }

        $room = Room::Query()->where('id_ruangan', $booking->ruangan_id)->first();
        $pic = User::Query()->where('id_user', $booking->user_id)->first();
        $members = $booking->getMembers();

        return [
            'success' => true,
            'data' => [
                'booking' => $booking,
                'room' => $room,
                'pic' => $pic,
                'members' => $members,
                'requiredMembers' => $this->getMinimumMembersRequired($booking),
                'maximumMembers' => $this->getMaximumMembersRequired($booking),
                'currentMembers' => $this->getMemberCount($booking),
            ],
        ];
    }
}
