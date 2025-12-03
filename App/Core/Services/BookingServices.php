<?php

namespace App\Core\Services;

use App\Models\Booking;
use App\Core\Repository\BookingRepository;
use App\Core\Exceptions\ValidationException;
use Carbon\Carbon;
use Exception;

class BookingServices
{
    private const STATE_TRANSITIONS = [
        'draft' => ['pending', 'cancelled', 'expired'],
        'pending' => ['verified', 'cancelled', 'expired'],
        'verified' => ['active', 'cancelled', 'no_show'],
        'active' => ['completed', 'no_show'],
        'completed' => [],
        'cancelled' => [],
        'no_show' => [],
    ];

    private const EDITABLE_STATES = ['draft', 'pending'];

    public function __construct(
        private BookingRepository $bookingRepo,
        private Logger $logger
    ) {
    }

    public function createDraft(array $data): Booking
    {
        $booking = new Booking();
        $booking->user_id = auth()->user()->id_user;
        $booking->ruangan_id = $data['ruangan_id'];
        $booking->tanggal_booking = Carbon::now()->format('Y-m-d');
        $booking->tanggal_penggunaan_ruang = $data['tanggal_penggunaan_ruang'];
        $booking->waktu_mulai = $data['waktu_mulai'];
        $booking->waktu_selesai = $data['waktu_selesai'];
        $booking->tujuan = $data['tujuan'] ?? '';
        $booking->status = 'draft';
        $booking->invite_token = bin2hex(random_bytes(16));
        $booking->save();
        $this->logger->info('Booking Created', [
            'pic' => auth()->user()->nama,
            'status' => $booking->status,
            'booking_id' => $booking->id_booking,
        ]);
        return $booking;
    }

    public function updateBooking(int $bookingId, array $data): Booking
    {
        $booking = $this->bookingRepo->findById($bookingId);
        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }
        if (!in_array($booking->status, self::EDITABLE_STATES)) {
            throw new Exception('Booking tidak dapat diubah lagi.');
        }
        if ($booking->user_id !== auth()->user()->id_user) {
            throw new Exception('Anda tidak memiliki akses untuk edit booking ini');
        }
        // Controller has already validated, just update
        $booking->tanggal_penggunaan_ruang = $data['tanggal_penggunaan_ruang'];
        $booking->waktu_mulai = $data['waktu_mulai'];
        $booking->waktu_selesai = $data['waktu_selesai'];
        $booking->tujuan = $data['tujuan'] ?? $booking->tujuan;
        $booking->save();
        $this->logger->info('Booking Updated', [
            'pic' => auth()->user()->nama,
            'status' => $booking->status,
            'booking_id' => $booking->id_booking,
        ]);
        return $booking;
    }

    public function transitionTo(int $bookingId, string $newStatus, ?string $reason = null): bool
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        $currentStatus = $booking->status;

        if (!$this->canTransitionTo($currentStatus, $newStatus)) {
            throw new Exception("Invalid state transition from {$currentStatus} to {$newStatus}");
        }

        $oldStatus = $booking->status;
        $booking->status = $newStatus;

        if ($newStatus === 'verified' && empty($booking->checkin_code)) {
            $booking->checkin_code = $this->generateCheckinCode();
        }

        $booking->save();

        $this->logger->info('Booking Transitioned', [
            'pic' => auth()->user()->nama,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'booking_id' => $booking->id_booking,
            'reason' => $reason,
        ]);

        return true;
    }

    public function submitForApproval(int $bookingId): bool
    {
        return $this->transitionTo($bookingId, 'pending', 'Submitted for approval');
    }

    public function approveBooking(int $bookingId): bool
    {
        return $this->transitionTo($bookingId, 'verified', 'Approved by admin');
    }

    public function cancelBooking(int $bookingId, string $reason = 'User Cancelled'): bool
    {
        return $this->transitionTo($bookingId, 'cancelled', $reason);
    }

    public function activateBooking(int $bookingId): bool
    {
        return $this->transitionTo($bookingId, 'active', 'Booking activated');
    }

    public function completeBooking(int $bookingId): bool
    {
        return $this->transitionTo($bookingId, 'completed', 'Booking completed');
    }

    public function markNoShow(int $bookingId): bool
    {
        return $this->transitionTo($bookingId, 'no_show', 'User marked as no show');
    }

    private function canTransitionTo(string $currentStatus, string $newStatus): bool
    {
        return in_array($newStatus, self::STATE_TRANSITIONS[$currentStatus] ?? []);
    }

    private function generateCheckinCode(): string
    {
        return strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    public function validateBookingRules(array $data, array $files, $user): void
    {
        $this->validateUserStatus($user);
        $this->validateUserLimit($user);
        $this->validateRoomNotMaintenance($data['ruangan_id']);
        $this->validateRoomCapacity($data);
        $this->validateBookingTimeRules($data);
        $this->validateNoTimeOverlap($data);
        $this->validateNoParallelBooking($data, $user);
        $this->validateFileUploadRules($files, $user, $data);
    }

    private function validateUserStatus($user): void
    {
        if (!$user) {
            throw new Exception('Anda harus login terlebih dahulu');
        }

        if ($user->status === 'pending kubaca') {
            throw new Exception('Anda harus terverifikasi kubaca terlebih dahulu sebelum booking');
        }

        if ($user->status === 'rejected') {
            throw new Exception('Akun tidak dapat melakukan peminjaman ruangan, silahkan upload kembali kubaca di profile');
        }

        if ($user->status !== 'active') {
            throw new Exception('Status akun harus active untuk melakukan booking');
        }
    }

    private function validateUserLimit($user): void
    {
        $activeCount = $this->bookingRepo->countActiveBookings($user->id_user);

        if ($activeCount >= 1) {
            throw new Exception("Anda hanya dapat melakukan 1 peminjaman ruangan sekaligus");
        }
    }

    private function validateRoomNotMaintenance(int $roomId): void
    {
        $room = $this->bookingRepo->findRoomById($roomId);

        if (!$room) {
            throw new Exception('Ruangan tidak ditemukan');
        }

        if ($room['status_ruangan'] === 'unavailable') {
            throw new Exception('Ruangan sedang dalam perbaikan/maintenance');
        }
    }

    private function validateRoomCapacity(array $data): void
    {
        $room = $this->bookingRepo->findRoomById($data['ruangan_id']);

        if (!$room) {
            throw new Exception('Ruangan tidak ditemukan');
        }

        $participantCount = (int) ($data['participant_count'] ?? 1);

        if ($participantCount < $room['kapasitas_min']) {
            throw new Exception("Jumlah anggota minimal {$room['kapasitas_min']} orang");
        }

        if ($participantCount > $room['kapasitas_max']) {
            throw new Exception("Jumlah anggota maksimal {$room['kapasitas_max']} orang");
        }
    }

    private function validateBookingTimeRules(array $data): void
    {
        $bookingDate = Carbon::parse($data['tanggal_penggunaan_ruang']);
        $today = Carbon::today();

        if ($bookingDate->lt($today)) {
            throw new Exception('Tidak dapat booking tanggal yang sudah lewat');
        }

        $maxDate = $today->addDays(7);
        if ($bookingDate->gt($maxDate)) {
            throw new Exception('Booking hanya bisa dibuat untuk 7 hari ke depan');
        }

        $dayOfWeek = $bookingDate->dayOfWeek;
        if ($dayOfWeek === 0 || $dayOfWeek === 6) {
            throw new Exception('Booking tidak tersedia pada hari Sabtu dan Minggu');
        }

        $start = Carbon::parse($data['waktu_mulai']);
        $end = Carbon::parse($data['waktu_selesai']);

        if ($start->gte($end)) {
            throw new Exception('Waktu selesai harus lebih besar dari waktu mulai');
        }

        $durationMinutes = $start->diffInMinutes($end);

        if ($durationMinutes < 60) {
            throw new Exception('Durasi booking minimal 1 jam');
        }
        if ($durationMinutes > 180) {
            throw new Exception('Durasi booking maksimal 3 jam');
        }

        $dateStr = $bookingDate->format('Y-m-d');
        $startDateTime = Carbon::parse("$dateStr {$data['waktu_mulai']}");
        $endDateTime = Carbon::parse("$dateStr {$data['waktu_selesai']}");

        $openSession1 = Carbon::parse("$dateStr 08:00");
        $breakStart = Carbon::parse("$dateStr 11:00");
        $breakEnd = Carbon::parse("$dateStr 12:00");
        $closeSession2 = Carbon::parse("$dateStr 16:20");

        if ($dayOfWeek === 5) {
            $breakStart = Carbon::parse("$dateStr 11:00");
            $breakEnd = Carbon::parse("$dateStr 13:00");
        }

        if ($startDateTime->lt($openSession1) || $endDateTime->gt($closeSession2)) {
            throw new Exception('Booking harus dalam jam operasional (08:00-16:20)');
        }

        $isCrossingBreak =
            ($startDateTime->lt($breakEnd) && $endDateTime->gt($breakStart));

        if ($isCrossingBreak) {
            if ($dayOfWeek === 5) {
                throw new Exception('Booking tidak boleh melewati jam istirahat Jumat (11:00-13:00)');
            } else {
                throw new Exception('Booking tidak boleh melewati jam istirahat (12:00-13:00)');
            }
        }

        if ($bookingDate->isSameDay($today)) {
            $minTime = Carbon::now()->addMinutes(15);
            if ($startDateTime->lt($minTime)) {
                throw new Exception('Waktu mulai harus minimal 15 menit dari sekarang');
            }
        }
    }
    private function validateNoTimeOverlap(array $data, ?int $excludeBookingId = null): void
    {

        $conflicts = $this->bookingRepo->findConflictingBooking(
            $data['ruangan_id'],
            $data['tanggal_penggunaan_ruang'],
            $data['waktu_mulai'],
            $data['waktu_selesai'],
            $excludeBookingId
        );

        $date = $data['tanggal_penggunaan_ruang'];

        foreach ($conflicts as $booking) {
            $s1 = Carbon::parse("$date {$data['waktu_mulai']}");
            $e1 = Carbon::parse("$date {$data['waktu_selesai']}");
            $s2 = Carbon::parse("{$booking['tanggal_penggunaan_ruang']} {$booking['waktu_mulai']}");
            $e2 = Carbon::parse("{$booking['tanggal_penggunaan_ruang']} {$booking['waktu_selesai']}");
            if ($s1->lt($e2) && $e1->gt($s2)) {
                throw new Exception("Ruangan sudah dibooking pada waktu {$booking['waktu_mulai']}-{$booking['waktu_selesai']}");
            }
        }
    }
    private function validateNoParallelBooking(array $data, $user, ?int $excludeBookingId = null): void // FIX: Use $user param
    {
        $date = $data['tanggal_penggunaan_ruang'];

        // Check bookings where user is PIC
        $userBookings = $this->bookingRepo->findUserBookingsOnDate(
            $user->id_user,
            $date,
            $excludeBookingId
        );
        foreach ($userBookings as $booking) {
            $s1 = Carbon::parse("$date {$data['waktu_mulai']}");
            $e1 = Carbon::parse("$date {$data['waktu_selesai']}");
            $s2 = Carbon::parse("$date {$booking['waktu_mulai']}"); // Same date
            $e2 = Carbon::parse("$date {$booking['waktu_selesai']}");
            if ($s1->lt($e2) && $e1->gt($s2)) {
                throw new Exception("Anda sudah menjadi PIC booking lain pada waktu {$booking['waktu_mulai']}-{$booking['waktu_selesai']}");
            }
        }
        // Check bookings where user is a MEMBER
        $memberBookings = $this->bookingRepo->findUserMemberBookingsOnDate(
            $user->id_user,
            $date,
            $excludeBookingId
        );
        foreach ($memberBookings as $booking) {
            $s1 = Carbon::parse("$date {$data['waktu_mulai']}");
            $e1 = Carbon::parse("$date {$data['waktu_selesai']}");
            $s2 = Carbon::parse("$date {$booking['waktu_mulai']}");
            $e2 = Carbon::parse("$date {$booking['waktu_selesai']}");
            if ($s1->lt($e2) && $e1->gt($s2)) {
                throw new Exception("Anda sudah menjadi anggota booking lain pada waktu {$booking['waktu_mulai']}-{$booking['waktu_selesai']}");
            }
        }
    }
    private function validateFileUploadRules(array $files, $user, array $data): void
    {
        $room = $this->bookingRepo->findRoomById($data['ruangan_id']);

        if (!$room) {
            throw new Exception('Ruangan tidak ditemukan');
        }

        if ($room['status_ruangan'] === 'adminOnly') {

            if ($user->isAdmin()) {
                return;
            }

            if (!$user->isDosen()) {
                throw new Exception('Ruangan ini hanya dapat dipinjam oleh Admin atau Dosen');
            }
        }

        if (
            $room['status_ruangan'] === 'adminOnly' &&
            $room['requires_special_approval'] &&
            $user->isDosen()
        ) {
            if (empty($files['pegawai_file']['name'])) {
                throw new Exception('File pendukung wajib diunggah untuk meminjam ruangan ini');
            }

            if ($files['pegawai_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Gagal mengunggah file');
            }

            $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($files['pegawai_file']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                throw new Exception('File harus berformat: pdf, jpg, jpeg, png');
            }

            if (($files['pegawai_file']['size'] / 1024) > 2048) {
                throw new Exception('Ukuran file maksimal 2MB');
            }

            if (empty($data['pegawai_reason'])) {
                throw new Exception('Alasan peminjaman wajib diisi');
            }
        }
    }
}
