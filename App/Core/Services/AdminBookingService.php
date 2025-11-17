<?php

namespace App\Core\Services;

use App\Core\App;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;

class AdminBookingService {
    private const perPage = 20;

    public function listAllBookings(array $filters = []): array {
        $page = max(1, (int)($filters['page'] ?? 1));
        $perPage = (int)($filters['perPage'] ?? self::perPage);

        $queryFilters = [
            'nama_ruangan' => $filters['keyword'] ?? null,
            'jenis_ruangan' => $filters['jenis_ruangan'] ?? null,
            'status_ruangan' => $filters['status_ruangan'] ?? null,
        ];

        $bookings = Booking::findPaginated($page, $perPage, $queryFilters, [
            'only_available' => false,
        ]);

        return [
            'success' => true,
            'data' => [
                'bookings' => $bookings,
                'filters' => $queryFilters,
                'currentPage' => $page,
                'perPage' => $perPage,
                'total' => Booking::count($queryFilters),
                'statusOptions' => $this->getStatusOptions(),
            ],
        ];

        // $sql = "
        //     SELECT b.*, u.nama, r.nama_ruangan, id_feedback
        //     from booking b
        //     left join users u on b.user_id = u.id_user
        //     left join ruangan r on b.ruangan_id = r.id_ruangan
        //     left join feedback fb on b.id_booking = fb.booking_id
        //     order by b.created_at desc
        // ";

        // $stmt = App::$app->db->pdo->query($sql);
        // return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getStatusOptions(): array {
        return [
            'draft' => 'Draft',
            'pending' => 'Pending',
            'verified' => 'Verified',
            'active' => 'Active',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'expired' => 'Expired',
            'no_show' => 'No Show',
        ];
    }

    public function getAdminBookingDetail(int $bookingId): array {
        $booking = Booking::findOne(['id_booking' => $bookingId]);
        if (!$booking) {
            return [
                'success' => false,
                'message' => 'Booking tidak ditemukan.',
            ];
        }

        // if ($booking->status !== 'pending') {
        //     return [
        //         'success' => false,
        //         'message' => 'Hanya booking dengan status pending',
        //     ];
        // }

        $room = Room::findOne(['id_ruangan' => $booking->ruangan_id]);
        $pic = User::findOne(['id_user' => $booking->user_id]);
        $members = $booking->getMembers();

        return [
            'success' => true,
            'data' => [
                'booking' => $booking,
                'room' => $room,
                'pic' => $pic,
                'members' => $members,
            ],
        ];
    }

    public function verifyBooking(int $bookingId, int $adminId): array
    {
        $booking = Booking::findOne($bookingId);

        if (!$booking || $booking->status !== 'pending') {
            return [
                'success' => false,
                'message' => 'Booking tidak valid',
            ];
        }

        if (!$booking->checkin_code) {
            $booking->checkin_code = $this->generateCheckinCode();
        }

        $booking->status = 'verified';
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

            if (!EmailService::send($pic->email, $pic->nama, $subject, $emailBody)) {
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

        Logger::admin('verified booking', $adminId, "Booking #{$bookingId} verified");

        return [
            'success' => true,
            'message' => 'Booking disetujui.',
        ];
    }

    public function markBookingCompleted(int $bookingId, int $adminId): array
    {
        $booking = Booking::findOne($bookingId);

        if (!$booking || $booking->status !== 'active') {
            return [
                'success' => false,
                'message' => 'Booking tidak valid.',
            ];
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
            <p>Terima kasih sudah menggunakan ruangan <strong>{$room->nama_ruangan}</strong>. Booking kamu pada <strong>{$bookingDate}</strong> pukul <strong>{$booking->waktu_mulai} - {$booking->waktu_selesai}</strong> sudah ditandai selesai.</p>
            <p>Kami ingin mendengar pengalaman kamu. Silakan isi feedback melalui tautan berikut:</p>
            <p>Masukan kamu membantu kami meningkatkan layanan.</p>
            <p>Terima kasih,<br>Library Booking App</p>
            ";

            if (!EmailService::send($pic->email, $pic->nama, $subject, $emailBody)) {
                Logger::warning('Failed to send booking completion email', [
                    'booking_id' => $booking->id_booking,
                    'user_id' => $pic->id_user,
                ]);
            }
        } else {
            Logger::warning('Booking Owner not found while sending completion email', [
                'booking_id' => $booking->id_booking,
                'user_id' => $booking->user_id,
            ]);
        }

        Logger::admin('completed booking', $adminId, "Booking #{$bookingId} marked as completed");

        return [
            'success' => true,
            'message' => 'Booking selesai.',
        ];
    }

    public function activateBooking(int $bookingId, string $code, int $adminId): array
    {
        $booking = Booking::findOne($bookingId);
        if (!$booking || $booking->status !== 'verified') {
            return [
                'success' => false,
                'message' => 'Booking tidak valid untuk check-in.',
            ];
        }

        $code = strtoupper(trim($code));
        if ($code === '' || strtoupper((string)$booking->checkin_code) !== $code) {
            return [
                'success' => false,
                'message' => 'Kode check-in tidak sesuai.',
            ];
        }

        $booking->status = 'active';
        if (!$booking->save()) {
            return [
                'success' => false,
                'message' => 'Gagal memperbarui status booking.',
            ];
        }

        $pic = User::findOne(['id_user' => $booking->user_id]);
        $room = Room::findOne(['id_ruangan' => $booking->ruangan_id]);
        if ($pic instanceof User && $room instanceof Room) {
            $subject = 'Check-in Berhasil | Library Booking App';
            $bookingDate = date('d M Y', strtotime($booking->tanggal_penggunaan_ruang));
            $body = "
                <p>Hai <strong>{$pic->nama}</strong>,</p>
                <p>Kode check-in untuk booking ruangan <strong>{$room->nama_ruangan}</strong> telah divalidasi.</p>
                <p><strong>Tanggal:</strong> {$bookingDate}</p>
                <p><strong>Waktu:</strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
                <p>Silakan gunakan ruangan sesuai ketentuan.</p>
                <p>Library Booking App</p>
            ";
            EmailService::send($pic->email, $pic->nama, $subject, $body);
        }

        Logger::admin('activated booking', $adminId, "Booking #{$bookingId} activated with code {$code}");

        return [
            'success' => true,
            'message' => 'Check-in berhasil divalidasi.',
        ];
    }

    public function cancelBooking(int $bookingId, int $adminId, ?string $reason = null): array
    {
        $booking = Booking::findOne($bookingId);
        if (!$booking) {
            return [
                'success' => false,
                'message' => 'Booking tidak ditemukan.',
            ];
        }

        if ($booking->status === 'cancelled') {
            return [
                'success' => false,
                'message' => 'Booking sudah dibatalkan.',
            ];
        }

        $booking->status = 'cancelled';
        if (!$booking->save()) {
            return [
                'success' => false,
                'message' => 'Gagal membatalkan booking.',
            ];
        }

        $pic = User::findOne(['id_user' => $booking->user_id]);
        $room = Room::findOne(['id_ruangan' => $booking->ruangan_id]);
        if ($pic instanceof User && $room instanceof Room) {
            $subject = 'Booking Dibatalkan | Library Booking App';
            $body = "
                <p>Hai <strong>{$pic->nama}</strong>,</p>
                <p>Booking ruangan <strong>{$room->nama_ruangan}</strong> telah dibatalkan oleh admin.</p>
                " . ($reason ? "<p><strong>Alasan:</strong> {$reason}</p>" : '') . "
                <p>Silakan hubungi admin jika membutuhkan bantuan.</p>
            ";
            EmailService::send($pic->email, $pic->nama, $subject, $body);
        }

        $logDetails = "Booking #{$bookingId} cancelled";
        if ($reason) {
            $logDetails .= " ({$reason})";
        }
        Logger::admin('cancelled booking', $adminId, $logDetails);

        return [
            'success' => true,
            'message' => 'Booking telah dibatalkan.',
        ];
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