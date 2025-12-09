<?php

namespace App\Core\Services;

use App\Core\Repository\BookingRepository;
use App\Core\Repository\InvitationRepository;
use App\Models\Booking;
use Carbon\Carbon;
use Exception;
use App\Core\Paginator;
use App\Core\Repository\FeedbackRepository;

class BookingServices
{
    private const STATE_TRANSITIONS = [
        'draft' => ['pending', 'cancelled', 'expired'],
        'pending' => ['verified', 'cancelled', 'expired', 'draft'],
        'verified' => ['active', 'cancelled', 'no_show'],
        'active' => ['completed', 'no_show'],
        'completed' => [],
        'cancelled' => [],
        'no_show' => [],
        'expired' => [],
    ];
    public function __construct(
        private BookingRepository $bookingRepo,
        private Logger $logger,
        private FeedbackRepository $feedbackRepo,
        private InvitationRepository $invitationRepo
    ) {
    }

    public function getBookingById(int $id): ?Booking
    {
        return $this->bookingRepo->findByIdWithDetails($id);
    }
    public function getBookingsByUser(int $userId, array $filters = [], int $perPage = 15, int $page = 1): Paginator
    {
        return $this->bookingRepo->getUserBookings($userId, $filters, $perPage, $page);
    }
    public function getAllBookings(array $filters = [], int $perPage = 15, int $page = 1): Paginator
    {
        return $this->bookingRepo->getAllBookings($filters, $perPage, $page);
    }
    public function getBookingMembers(int $bookingId): array
    {
        return $this->bookingRepo->getBookingMembers($bookingId);
    }
    public function getBlockedDates(): array
    {
        return $this->bookingRepo->getBlockedDates();
    }

    public function getAllRooms(): array
    {
        return $this->bookingRepo->getAllRooms();
    }

    public function getAllUsers(): array
    {
        return $this->bookingRepo->getAllUsers();
    }

    public function adminCreateBooking(array $data, int $targetUserId): Booking
    {
        $user = $this->bookingRepo->findUserById($targetUserId);

        if (!$user) {
            throw new Exception('User tidak ditemukan');
        }

        $booking = new Booking();
        $booking->user_id = $targetUserId;
        $booking->ruangan_id = $data['ruangan_id'];
        $booking->tanggal_booking = Carbon::now()->format('Y-m-d');
        $booking->tanggal_penggunaan_ruang = $data['tanggal_penggunaan_ruang'];
        $booking->waktu_mulai = $data['waktu_mulai'];
        $booking->waktu_selesai = $data['waktu_selesai'];
        $booking->tujuan = $data['tujuan'] ?? '';
        $booking->status = 'verified';
        $booking->checkin_code = $this->generateCheckinCode();
        $booking->invite_token = $this->generateInviteToken();

        $booking->save();
        $this->logger->info('Admin Created Booking', [
            'booking_id' => $booking->id_booking,
            'for_user' => $targetUserId,
            'status' => $booking->status,
        ]);
        return $booking;
    }

    public function adminUpdateBooking(int $bookingId, array $data): Booking
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        if (!in_array($booking->status, ['draft', 'pending', 'verified'])) {
            throw new Exception('Booking dengan status ' . $booking->status . ' tidak dapat diedit');
        }

        // Update fields if provided
        if (isset($data['ruangan_id'])) {
            $booking->ruangan_id = $data['ruangan_id'];
        }

        if (isset($data['tanggal_penggunaan_ruang'])) {
            $booking->tanggal_penggunaan_ruang = $data['tanggal_penggunaan_ruang'];
        }

        if (isset($data['waktu_mulai'])) {
            $booking->waktu_mulai = $data['waktu_mulai'];
        }

        if (isset($data['waktu_selesai'])) {
            $booking->waktu_selesai = $data['waktu_selesai'];
        }

        if (isset($data['tujuan'])) {
            $booking->tujuan = $data['tujuan'];
        }

        if (isset($data['status'])) {
            $booking->status = $data['status'];
            if ($data['status'] === 'verified' && empty($booking->checkin_code)) {
                $booking->checkin_code = $this->generateCheckinCode();
            }
        }

        $booking->save();

        $this->logger->info('Admin Updated Booking', [
            'booking_id' => $bookingId,
            'updated_fields' => array_keys($data),
        ]);

        return $booking;
    }

    public function deleteBooking(int $bookingId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);
        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        // Hard delete
        $this->bookingRepo->delete($bookingId);

        $this->logger->info('Booking Deleted', [
            'booking_id' => $bookingId,
        ]);
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
        $booking->invite_token = $this->generateInviteToken();
        $booking->save();

        $this->logger->info('Booking Created', [
            'booking_id' => $booking->id_booking,
            'pic' => auth()->user()->nama,
            'status' => $booking->status,
        ]);

        return $booking;
    }

    public function getBookingForUser(int $bookingId, int $userId, bool $isAdmin = false): array
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception("Booking tidak ditemukan");
        }

        $bookings = $this->bookingRepo->findByIdWithDetails($bookingId);
        $isPic = (int) $booking->user_id === $userId;
        $isMember = $this->bookingRepo->isMemberOfBooking($bookingId, $userId);

        if (!$isPic && !$isMember && !$isAdmin) {
            throw new Exception('Anda tidak memiliki akses ke booking ini');
        }

        $members = $this->bookingRepo->getBookingMembers($bookingId);

        $pic = null;
        $otherMembers = [];
        foreach ($members as $member) {
            if ($member['is_owner'] == 1) {
                $pic = $member;
            } else {
                $otherMembers[] = $member;
            }
        }

        $allMembers = $pic ? array_merge([$pic], $otherMembers) : $otherMembers;
        $canSubmit = $bookings->status === 'draft' && ($bookings->required_members <= 0 || $bookings->current_members >= $bookings->required_members);

        return [
            'booking' => $bookings,
            'pic' => $pic,
            'members' => $otherMembers,
            'isPic' => $isPic,
            'isMember' => $isMember,
            'canSubmit' => $canSubmit,
            'allMembers' => $allMembers,
        ];
    }

    public function transitionTo(int $bookingId, string $newStatus, ?string $reason = null): bool
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception("Booking tidak ditemukan");
        }

        $currentStatus = $booking->status;

        if (!$this->canTransitionTo($currentStatus, $newStatus)) {
            throw new Exception("Invalid status transition from {$currentStatus} to {$newStatus}");
        }

        $oldStatus = $booking->status;
        $booking->status = $newStatus;

        if ($newStatus === 'verified' && empty($booking->checkin_code)) {
            $booking->checkin_code = $this->generateCheckinCode();
        }

        $booking->save();

        $this->logger->info("Booking Transitioned", [
            'booking_id' => $booking->id_booking,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'reason' => $reason,
        ]);

        return true;
    }

    private function canTransitionTo(string $currentStatus, string $newStatus): bool
    {
        return in_array($newStatus, self::STATE_TRANSITIONS[$currentStatus] ?? []);
    }

    private function generateCheckinCode(): string
    {
        return strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }
    private function generateInviteToken(): string
    {
        return strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }

    public function validateBookingRules(array $data, $user): void
    {
        $this->validateRequiredFields($data);
        $this->validateNotPastBookings($data);
        $this->validateTimeOrder($data);
        $this->validateDuration($data);
        $this->validateSessionHours($data);
        $this->validateBreakTime($data);
        $this->validateMaxDaysAhead($data);
        $this->validateMinLeadTime($data);
        $this->validateUserStatus($user);
        $this->validateRoomAvailable($data['ruangan_id']);
        $this->validateUserRoleCanBookRoom($user, $data['ruangan_id']);
        $this->validateOneBookingPerDay($user->id_user, $data['tanggal_penggunaan_ruang']);
        $this->validateDateNotBlocked($data['tanggal_penggunaan_ruang'], $data['ruangan_id']);
        $this->validateHasPendingFeedback($user->id_user);
    }

    public function validateUpdateDraftRules(array $data, int $excludeBookingId): void
    {
        $this->validateRequiredFields($data);
        $this->validateNotPastBookings($data);
        $this->validateTimeOrder($data);
        $this->validateDuration($data);
        $this->validateSessionHours($data);
        $this->validateBreakTime($data);
        $this->validateMaxDaysAhead($data);
        $this->validateMinLeadTime($data);
        $this->validateRoomAvailable($data['ruangan_id']);
        $this->validateDateNotBlocked($data['tanggal_penggunaan_ruang'], $data['ruangan_id']);
    }

    /**
     * Validate reschedule-specific rules (skips user status and one-booking-per-day checks
     * since the user already has an approved booking)
     */
    public function validateRescheduleRules(array $data): void
    {
        $this->validateNotPastBookings($data);
        $this->validateTimeOrder($data);
        $this->validateDuration($data);
        $this->validateSessionHours($data);
        $this->validateBreakTime($data);
        $this->validateMaxDaysAhead($data);
        $this->validateMinLeadTime($data);
        $this->validateRoomAvailable($data['ruangan_id']);
        $this->validateDateNotBlocked($data['tanggal_penggunaan_ruang'], $data['ruangan_id']);
    }

    public function validateNoTimeConflicts(array $data, int $userId, ?int $excludeBookingId = null): void
    {
        $this->validateRoomNoOverlap($data, $excludeBookingId);
        $this->validatePicNoOverlap($data, $userId, $excludeBookingId);
        $this->validateMemberNoOverlap($data, $userId, $excludeBookingId);
    }

    public function submitForApproval(int $bookingId, int $userId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        if ((int) $booking->user_id !== $userId) {
            throw new Exception('Hanya PIC yang dapat submit booking');
        }

        if ($booking->status !== 'draft') {
            throw new Exception('Hanya booking dengan status draft yang bisa di submit');
        }

        $this->validateMinimumCapacity($bookingId);

        $this->invitationRepo->rejectAllPendingForBooking($bookingId);

        $this->transitionTo($bookingId, 'pending', 'Submitted by PIC for approval');
    }

    public function addMember(int $bookingId, int $memberUserId, int $requestingUserId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception("Booking tidak ditemukan");
        }

        $isAdmin = auth()->user()->id_role === 1;

        if (!$isAdmin && (int) $booking->user_id !== $requestingUserId) {
            throw new Exception('Hanya PIC yang dapat menambahkan anggota');
        }

        if (!$isAdmin && $booking->status !== 'draft') {
            throw new Exception('Hanya booking dengan status draft yang bisa menambahkan anggota');
        }

        if ($memberUserId === (int) $booking->user_id) {
            throw new Exception('PIC tidak dapat menambahkan diri sendiri sebagai anggota');
        }

        if ($this->bookingRepo->isMemberOfBooking($bookingId, $memberUserId)) {
            throw new Exception('Anggota sudah terdaftar dalam booking');
        }

        $this->validateMemberCanJoin($memberUserId, $bookingId);

        $room = $this->bookingRepo->findByRoomId($booking->ruangan_id);
        if (!$room) {
            throw new Exception('Ruangan tidak ditemukan');
        }

        $currentMemberCount = $this->bookingRepo->getMemberCount($bookingId);
        $totalParticipants = $currentMemberCount + 1 + 1;

        if ($totalParticipants > $room['kapasitas_max']) {
            throw new Exception("Kapasitas maksimal {$room['kapasitas_max']} orang sudah tercapai");
        }

        $this->bookingRepo->addMember($bookingId, $memberUserId);

        $this->logger->info('Member added to booking', [
            'booking_id' => $bookingId,
            'member_user_id' => $memberUserId,
            'added_by' => $requestingUserId,
        ]);
    }

    public function addMemberByIdentifier(int $bookingId, string $identifier): void
    {
        $user = $this->bookingRepo->findUserByIdentifier($identifier);
        if (!$user) {
            throw new Exception('User dengan email/NIM/NIP/nama tersebut tidak ditemukan');
        }

        $this->addMember($bookingId, $user->id_user, auth()->user()->id_user);
    }

    public function joinViaInviteToken(string $token, int $userId): array
    {
        $booking = $this->bookingRepo->findByInviteToken($token);

        if (!$booking) {
            throw new Exception('Kode undangan tidak valid');
        }

        if ($booking->status !== 'draft') {
            throw new Exception('Tidak dapat bergabung - booking sudah ' . $booking->status);
        }

        if ($userId === (int) $booking->user_id) {
            throw new Exception('Anda adalah PIC dari booking ini');
        }

        if ($this->bookingRepo->isMemberOfBooking($booking->id_booking, $userId)) {
            throw new Exception('Anda sudah terdaftar dalam booking ini');
        }

        $existing = $this->invitationRepo->findByBookingAndUser($booking->id_booking, $userId);

        // Case 1: Already requested via token (self-request pending)
        if ($existing && $existing->status === 'pending' && $existing->invited_by_user_id === null) {
            throw new Exception('Anda sudah mengajukan permintaan bergabung');
        }

        // Case 2: PIC already invited this user - auto accept!
        if ($existing && $existing->status === 'pending' && $existing->invited_by_user_id !== null) {
            $this->validateMemberCanJoin($userId, $booking->id_booking);
            $this->invitationRepo->accept($existing->id_invitation);
            $this->bookingRepo->addMember($booking->id_booking, $userId);
            $this->logger->info('User auto-accepted PIC invitation via token', [
                'booking_id' => $booking->id_booking,
                'user_id' => $userId,
            ]);
            return ['booking_id' => $booking->id_booking, 'auto_joined' => true];
        }

        // Check pending limit
        $userPendingCount = $this->invitationRepo->countPendingForUser($userId);
        if ($userPendingCount >= 3) {
            throw new Exception('Anda sudah memiliki 3 undangan yang belum direspon');
        }

        // Check capacity
        $room = $this->bookingRepo->findByRoomId($booking->ruangan_id);
        if (!$room) {
            throw new Exception('Ruangan tidak ditemukan');
        }
        $currentMemberCount = $this->bookingRepo->getMemberCount($booking->id_booking);
        $pendingCount = count($this->invitationRepo->getPendingForBooking($booking->id_booking));
        $totalParticipants = $currentMemberCount + $pendingCount + 1 + 1;
        if ($totalParticipants > $room['kapasitas_max']) {
            throw new Exception("Kapasitas maksimal {$room['kapasitas_max']} orang sudah tercapai");
        }

        // Case 3: Old invitation exists (accepted/rejected) - reset as self-request
        if ($existing) {
            $existing->status = 'pending';
            $existing->invited_by_user_id = null;
            $existing->save();
        } else {
            // Case 4: No existing - create new self-request
            $this->invitationRepo->create([
                'booking_id' => $booking->id_booking,
                'invited_user_id' => $userId,
                'invited_by_user_id' => null,
                'status' => 'pending',
            ]);
        }

        $this->logger->info('User requested to join via invite token', [
            'booking_id' => $booking->id_booking,
            'user_id' => $userId,
        ]);

        return ['booking_id' => $booking->id_booking, 'auto_joined' => false];
    }

    public function leaveBooking(int $bookingId, int $userId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);
        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        if ((int) $booking->user_id === $userId) {
            throw new Exception('PIC tidak dapat meninggalkan booking, gunakan cancel booking');
        }

        if (!$this->bookingRepo->isMemberOfBooking($bookingId, $userId)) {
            throw new Exception('Anda bukan anggota dari booking ini');
        }

        if ($booking->status !== 'draft') {
            throw new Exception('Tidak dapat meninggalkan booking yang sudah berlangsung');
        }
        $this->bookingRepo->removeMember($bookingId, $userId);
        $this->logger->info('Member left booking', [
            'booking_id' => $bookingId,
            'member_user_id' => $userId,
        ]);
    }

    public function kickMember(int $bookingId, int $memberId, int $picId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        $isAdmin = auth()->user()->id_role === 1;

        if (!$isAdmin && (int) $booking->user_id !== $picId) {
            throw new Exception('Hanya PIC yang dapat mengeluarkan anggota');
        }

        if ($memberId === $picId) {
            throw new Exception('Tidak dapat mengeluarkan diri sendiri');
        }

        if (!$isAdmin && $booking->status !== 'draft') {
            throw new Exception('Tidak dapat mengeluarkan anggota dari booking yang sudah berlangsung');
        }

        if (!$this->bookingRepo->isMemberOfBooking($bookingId, $memberId)) {
            throw new Exception('User bukan anggota booking ini');
        }

        $this->bookingRepo->removeMember($bookingId, $memberId);

        $this->logger->info('Member kicked from booking', [
            'booking_id' => $bookingId,
            'kicked_user_id' => $memberId,
            'kicked_by' => $picId,
        ]);
    }

    public function approveBooking(int $bookingId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        if ($booking->status !== 'pending') {
            throw new Exception('Booking tidak dapat diapprove - status booking adalah ' . $booking->status);
        }

        $this->revalidateBookingForApproval($bookingId);

        $this->transitionTo($bookingId, 'verified', 'Approved by admin');
    }

    public function rejectBooking(int $bookingId, string $reason = 'Rejected by admin'): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        if ($booking->status !== 'pending') {
            throw new Exception('Hanya booking dengan status pending yang dapat di reject');
        }

        $this->transitionTo($bookingId, 'cancelled', $reason);
    }

    public function activateBooking(int $bookingId, string $checkinCode): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        if ($booking->status !== 'verified') {
            throw new Exception('Booking harus berstatus verified untuk check-in');
        }

        if ($booking->checkin_code !== strtoupper($checkinCode)) {
            throw new Exception('Kode check-in tidak valid');
        }

        // $startDateTime = Carbon::parse("{$booking->tanggal_penggunaan_ruang} {$booking->waktu_mulai}");
        // $now = Carbon::now();

        // if ($now->lt($startDateTime)) {
        //     Carbon::setLocale('id');
        //     $diff = $now->diff($startDateTime);
        //     $parts = [];
        //     if ($diff->d > 0)
        //         $parts[] = "{$diff->d} hari";
        //     if ($diff->h > 0)
        //         $parts[] = "{$diff->h} jam";
        //     if ($diff->i > 0 && count($parts) < 2)
        //         $parts[] = "{$diff->i} menit";
        //     $timeStr = implode(' ', $parts);
        //     throw new Exception("Check-in belum dibuka. Mulai dalam {$timeStr}");
        // }

        $booking->checkin_code = null;
        $booking->invite_token = null;
        $booking->save();

        $this->transitionTo($bookingId, 'active', 'Checked in by admin');
    }

    public function handleNoShow(int $bookingId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        if ($booking->status !== 'verified') {
            throw new Exception('No-show hanya untuk booking verified');
        }

        $startDateTime = Carbon::parse("{$booking->tanggal_penggunaan_ruang} {$booking->waktu_mulai}");
        $now = Carbon::now();

        $minutesSinceStart = $startDateTime->diffInMinutes($now, false);
        if ($minutesSinceStart < 10) {
            throw new Exception('No-show hanya setelah 10 menit dari waktu mulai');
        }

        $this->transitionTo($bookingId, 'no_show', 'No show - tidak check-in dalam 10 menit');

        $this->applyNoShowPenalty($booking->user_id);
    }

    public function completeBooking(int $bookingId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        if ($booking->status !== 'active') {
            throw new Exception('Hanya booking dengan status active yang dapat di complete');
        }

        $this->transitionTo($bookingId, 'completed', 'Booking completed by admin');
    }

    public function autoCompleteBooking(int $bookingId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            return;
        }

        if ($booking->status !== 'active') {
            return;
        }

        $endDateTime = Carbon::parse("{$booking->tanggal_penggunaan_ruang} {$booking->waktu_selesai}");
        $now = Carbon::now();

        if ($now->gte($endDateTime)) {
            $this->transitionTo($bookingId, 'completed', 'Auto-completed after end time');
        }
    }

    public function cancelBooking(int $bookingId, int $userId, string $reason = 'Cancelled by user'): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        $user = $this->bookingRepo->findUserById($userId);
        $isAdmin = $user && $user['id_role'] === 1;

        if (!$isAdmin && (int) $booking->user_id !== $userId) {
            throw new Exception('Hanya PIC atau Admin yang dapat membatalkan booking');
        }

        if (in_array($booking->status, ['completed', 'cancelled', 'no_show'])) {
            throw new Exception('Booking tidak dapat dibatalkan');
        }

        if ($booking->status === 'active') {
            throw new Exception('Booking yang sudah aktif tidak dapat dibatalkan');
        }

        if ($booking->status === 'verified' && !$isAdmin) {
            $startDateTime = Carbon::parse("{$booking->tanggal_penggunaan_ruang} {$booking->waktu_mulai}");
            $now = Carbon::now();

            $minutesUntilStart = $now->diffInMinutes($startDateTime, false);

            if ($minutesUntilStart < 15) {
                throw new Exception('Tidak dapat membatalkan booking kurang dari 15 menit sebelum waktu mulai');
            }

            $this->applyLateCancellationPenalty($booking->user_id);
        }


        $this->transitionTo($bookingId, 'cancelled', $reason);
    }

    public function expireDraft(int $bookingId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            return;
        }

        if ($booking->status !== 'draft') {
            return;
        }

        $startDateTime = Carbon::parse("{$booking->tanggal_penggunaan_ruang} {$booking->waktu_mulai}");
        $now = Carbon::now();

        $minutesUntilStart = $now->diffInMinutes($startDateTime, false);

        if ($minutesUntilStart < 15) {
            $this->transitionTo($bookingId, 'expired', 'Draft expired - not submitted before 15 min deadline');
        }
    }

    public function expirePending(int $bookingId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            return;
        }

        if ($booking->status !== 'pending') {
            return;
        }

        $startDateTime = Carbon::parse("{$booking->tanggal_penggunaan_ruang} {$booking->waktu_mulai}");
        $now = Carbon::now();

        $minutesUntilStart = $now->diffInMinutes($startDateTime, false);

        if ($minutesUntilStart < 5) {
            $this->transitionTo($bookingId, 'cancelled', 'Auto-cancelled - not verified 5 minutes before start');
        }
    }

    public function rescheduleBooking(int $bookingId, array $newData, int $userId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        $user = $this->bookingRepo->findUserById($userId);
        $isAdmin = $user && $user['id_role'] === 1;

        if (!$isAdmin && (int) $booking->user_id !== $userId) {
            throw new Exception('Hanya PIC atau Admin yang dapat reschedule booking');
        }

        if ($booking->status !== 'verified') {
            throw new Exception('Hanya booking dengan status verified yang dapat di reschedule');
        }

        $startDateTime = Carbon::parse("{$booking->tanggal_penggunaan_ruang} {$booking->waktu_mulai}");
        $now = Carbon::now();

        if ($now->gte($startDateTime)) {
            throw new Exception('Tidak dapat reschedule booking yang sudah dimulai');
        }

        $minutesUntilStart = $now->diffInMinutes($startDateTime, false);

        if ($minutesUntilStart < 15) {
            throw new Exception('Tidak dapat reschedule kurang dari 15 menit sebelum waktu mulai');
        }

        if ($booking->has_been_rescheduled ?? false) {
            throw new Exception('Booking hanya dapat di-reschedule 1 kali');
        }

        // Merge existing booking data with new reschedule data for validation
        $validationData = array_merge([
            'ruangan_id' => $booking->ruangan_id,
            'tujuan' => $booking->tujuan,
        ], $newData);

        // Use auth()->user() for validation as validateBookingRules expects a user object
        $this->validateRescheduleRules($validationData);
        $this->validateNoTimeConflicts($validationData, $booking->user_id, $bookingId);

        $booking->tanggal_penggunaan_ruang = $newData['tanggal_penggunaan_ruang'];
        $booking->waktu_mulai = $newData['waktu_mulai'];
        $booking->waktu_selesai = $newData['waktu_selesai'];
        $booking->has_been_rescheduled = true;

        $booking->status = 'pending';
        $booking->checkin_code = null;
        $booking->save();

        $this->logger->info('Booking Rescheduled', [
            'booking_id' => $bookingId,
            'old_date' => $startDateTime->format('Y-m-d H:i'),
            'new_date' => "{$newData['tanggal_penggunaan_ruang']} {$newData['waktu_mulai']}",
            'rescheduled_by' => $userId,
        ]);
    }

    public function applyNoShowPenalty(int $userId): void
    {
        $user = $this->bookingRepo->findUserById($userId);

        if (!$user) {
            return;
        }

        $newWarningLevel = ($user['peringatan'] ?? 0) + 1;
        $this->bookingRepo->updateUserWarning($userId, $newWarningLevel);

        $this->logger->warning('No-show Penalty Applied', [
            'user_id' => $userId,
            'new_warning_level' => $newWarningLevel,
        ]);

        if ($newWarningLevel >= 3) {
            $suspendUntil = Carbon::now()->addDays(7)->format('Y-m-d');
            $this->bookingRepo->updateUserStatus($userId, 'suspended', $suspendUntil);

            $this->logger->warning('User Auto-Suspended', [
                'user_id' => $userId,
                'warning_level' => $newWarningLevel,
                'suspension_until' => $suspendUntil,
            ]);
        }
    }

    public function applyLateCancellationPenalty(int $userId): void
    {
        $user = $this->bookingRepo->findUserById($userId);

        if (!$user) {
            return;
        }

        $newWarningLevel = ($user['peringatan'] ?? 0) + 1;
        $this->bookingRepo->updateUserWarning($userId, $newWarningLevel);

        $this->logger->warning('Late Cancellation Penalty Applied', [
            'user_id' => $userId,
            'new_warning_level' => $newWarningLevel,
        ]);

        if ($newWarningLevel >= 3) {
            $suspendUntil = Carbon::now()->addDays(7)->format('Y-m-d');
            $this->bookingRepo->updateUserStatus($userId, 'suspended', $suspendUntil);

            $this->logger->warning('User Auto-Suspended', [
                'user_id' => $userId,
                'warning_level' => $newWarningLevel,
                'suspension_until' => $suspendUntil,
            ]);
        }
    }

    public function checkAndUnsuspendUser(int $userId): bool
    {
        $user = $this->bookingRepo->findUserById($userId);

        if (!$user || $user['status'] !== 'suspended') {
            return false;
        }

        if (empty($user['suspensi_terakhir'])) {
            return false;
        }

        $suspendUntil = Carbon::parse($user['suspensi_terakhir']);
        $now = Carbon::now();

        if ($now->gte($suspendUntil)) {
            $this->bookingRepo->updateUserWarning($userId, 0);
            $this->bookingRepo->updateUserStatus($userId, 'active');

            $this->logger->info('User Auto-Unsuspended', ['user_id' => $userId]);
            return true;
        }

        return false;
    }

    public function blockDateRange(string $dateBegin, string $dateEnd, ?int $ruanganId, string $reason, int $adminId): void
    {
        $begin = Carbon::parse($dateBegin);
        $end = Carbon::parse($dateEnd);

        if ($end->lt($begin)) {
            throw new Exception('Tanggal akhir harus setelah tanggal awal');
        }

        $this->bookingRepo->blockDateRange($begin->format('Y-m-d'), $end->format('Y-m-d'), $ruanganId, $reason, $adminId);

        $this->logger->info('Date Range Blocked', [
            'date_begin' => $begin->format('Y-m-d'),
            'date_end' => $end->format('Y-m-d'),
            'ruangan_id' => $ruanganId,
            'reason' => $reason,
            'blocked_by' => $adminId,
        ]);
    }

    public function unblockDate(int $blockedDateId): void
    {
        $this->bookingRepo->unblockDate($blockedDateId);
        $this->logger->info('Date Unblocked', ['blocked_date_id' => $blockedDateId]);
    }

    private function validateDateNotBlocked(string $date, int $ruanganId): void
    {
        if ($this->bookingRepo->isDateBlocked($date, $ruanganId)) {
            throw new Exception('Tanggal ini telah diblokir oleh admin');
        }
    }

    private function validateRequiredFields(array $data): void
    {
        if (empty($data['tanggal_penggunaan_ruang'])) {
            throw new Exception('Tanggal penggunaan ruang harus diisi');
        }

        if (empty($data['waktu_mulai'])) {
            throw new Exception('Waktu mulai harus diisi');
        }

        if (empty($data['waktu_selesai'])) {
            throw new Exception('Waktu selesai harus diisi');
        }

        if (empty($data['tujuan'])) {
            throw new Exception('Tujuan booking harus diisi');
        }
    }

    private function validateNotPastBookings(array $data): void
    {
        $bookingDate = Carbon::parse($data['tanggal_penggunaan_ruang']);
        $today = Carbon::today();
        if ($bookingDate->lt($today)) {
            throw new Exception('Tidak dapat booking tanggal yang sudah lewat');
        }

        if ($bookingDate->isSameDay($today)) {
            $startDateTime = Carbon::parse(
                $data['tanggal_penggunaan_ruang'] . ' ' . $data['waktu_mulai']
            );

            if ($startDateTime->lte(Carbon::now())) {
                throw new Exception('Waktu mulai harus lebih besar dari waktu sekarang');
            }
        }
    }

    private function validateTimeOrder(array $data): void
    {
        $start = Carbon::parse($data['waktu_mulai']);
        $end = Carbon::parse($data['waktu_selesai']);
        if ($start->gte($end)) {
            throw new Exception('Waktu selesai harus lebih besar dari waktu mulai');
        }
    }

    private function validateDuration(array $data): void
    {
        $start = Carbon::parse($data['waktu_mulai']);
        $end = Carbon::parse($data['waktu_selesai']);
        $durationMinutes = $start->diffInMinutes($end, false);

        if ($durationMinutes < 60) {
            throw new Exception('Durasi booking minimal 1 jam');
        }

        if ($durationMinutes > 180) {
            throw new Exception('Durasi booking maksimal 3 jam');
        }
    }

    private function validateSessionHours(array $data): void
    {
        $dateStr = $data['tanggal_penggunaan_ruang'];
        $startDateTime = Carbon::parse("$dateStr {$data['waktu_mulai']}");
        $endDateTime = Carbon::parse("$dateStr {$data['waktu_selesai']}");

        $session1Start = Carbon::parse("$dateStr 08:15");
        $session1End = Carbon::parse("$dateStr 10:55");
        $session2Start = Carbon::parse("$dateStr 13:15");
        $session2End = Carbon::parse("$dateStr 16:00");

        $inSession1 = $startDateTime->gte($session1Start) && $endDateTime->lte($session1End);
        $inSession2 = $startDateTime->gte($session2Start) && $endDateTime->lte($session2End);

        if (!$inSession1 && !$inSession2) {
            throw new Exception('Booking harus dalam sesi 1 (08:15-10:55) atau sesi 2 (13:15-16:00)');
        }
    }

    private function validateBreakTime(array $data): void
    {
        $bookingDate = Carbon::parse($data['tanggal_penggunaan_ruang']);
        $dateStr = $bookingDate->format('Y-m-d');
        $dayOfWeek = $bookingDate->dayOfWeek;

        $startDateTime = Carbon::parse("$dateStr {$data['waktu_mulai']}");
        $endDateTime = Carbon::parse("$dateStr {$data['waktu_selesai']}");

        if ($dayOfWeek === 5) { // Jumat
            $breakStart = Carbon::parse("$dateStr 11:00");
            $breakEnd = Carbon::parse("$dateStr 13:00");

            if ($startDateTime->lt($breakEnd) && $endDateTime->gt($breakStart)) {
                throw new Exception('Booking tidak boleh melewati jam istirahat Jumat (11:00-13:00)');
            }
        } else { // Senin - Kamis
            $breakStart = Carbon::parse("$dateStr 11:00");
            $breakEnd = Carbon::parse("$dateStr 12:00");

            if ($startDateTime->lt($breakEnd) && $endDateTime->gt($breakStart)) {
                throw new Exception('Booking tidak boleh melewati jam istirahat (11:00-12:00)');
            }
        }
    }

    private function validateMaxDaysAhead(array $data): void
    {
        $bookingDate = Carbon::parse($data['tanggal_penggunaan_ruang']);
        $today = Carbon::today();

        $maxDate = $today->copy();
        $workingDays = 0;
        while ($workingDays < 7) {
            $maxDate->addDay();
            if ($maxDate->isWeekday()) {
                $workingDays++;
            }
        }
        if ($bookingDate->gt($maxDate)) {
            throw new Exception('Booking hanya bisa dibuat untuk 7 hari kerja ke depan');
        }
        if ($bookingDate->isWeekend()) {
            throw new Exception('Booking tidak tersedia pada hari Sabtu dan Minggu');
        }
    }

    private function validateMinLeadTime(array $data): void
    {
        $bookingDate = Carbon::parse($data['tanggal_penggunaan_ruang']);
        $today = Carbon::today();

        if ($bookingDate->isSameDay($today)) {
            $dateStr = $bookingDate->format('Y-m-d');
            $startDateTime = Carbon::parse("$dateStr {$data['waktu_mulai']}");
            $minTime = Carbon::now()->addMinutes(15);
            if ($startDateTime->lt($minTime)) {
                throw new Exception('Waktu mulai harus minimal 15 menit dari sekarang');
            }
        }
    }

    private function validateUserStatus($user): void
    {
        if (!$user) {
            throw new Exception('Anda harus login terlebih dahulu');
        }

        if ($user->status === 'pending kubaca') {
            throw new Exception('Anda harus terverifikasi kubaca terlebih dahulu');
        }

        if ($user->status === 'rejected') {
            throw new Exception('Akun tidak dapat melakukan peminjaman, silahkan upload kembali kubaca');
        }

        if ($user->status === 'suspended') {
            throw new Exception('Akun sedang dalam masa suspensi');
        }

        if ($user->status !== 'active') {
            throw new Exception('Status akun harus active untuk melakukan booking');
        }
    }

    private function validateRoomAvailable(int $roomId): void
    {
        $room = $this->bookingRepo->findByRoomId($roomId);

        if (!$room) {
            throw new Exception('Ruangan tidak ditemukan');
        }

        if ($room['status_ruangan'] === 'unavailable') {
            throw new Exception('Ruangan sedang dalam perbaikan/maintenance');
        }
    }

    private function validateUserRoleCanBookRoom($user, int $roomId): void
    {
        $room = $this->bookingRepo->findByRoomId($roomId);

        if (!$room) {
            throw new Exception('Ruangan tidak ditemukan');
        }

        if ($room['status_ruangan'] === 'adminOnly') {
            if (!$user->isAdmin() && !$user->isDosen() && !$user->isTendik()) {
                throw new Exception('Ruangan ini hanya dapat dipinjam oleh Admin, Dosen, atau Tendik');
            }
        }
    }

    private function validateOneBookingPerDay(int $userId, string $date): void
    {
        $existingBookings = $this->bookingRepo->findUserBookingsOnDate($userId, $date);

        if (count($existingBookings) > 0) {
            throw new Exception('Anda hanya dapat melakukan 1 booking per hari');
        }
    }

    private function validateRoomNoOverlap(array $data, ?int $excludeBookingId = null): void
    {
        $conflicts = $this->bookingRepo->findConflictingBookings(
            $data['ruangan_id'],
            $data['tanggal_penggunaan_ruang'],
            $excludeBookingId
        );

        $date = $data['tanggal_penggunaan_ruang'];
        $s1 = Carbon::parse("$date {$data['waktu_mulai']}");
        $e1 = Carbon::parse("$date {$data['waktu_selesai']}");

        foreach ($conflicts as $booking) {
            $s2 = Carbon::parse("{$booking->tanggal_penggunaan_ruang} {$booking->waktu_mulai}");
            $e2 = Carbon::parse("{$booking->tanggal_penggunaan_ruang} {$booking->waktu_selesai}");

            if ($s1->lt($e2) && $e1->gt($s2)) {
                throw new Exception("Ruangan sudah dibooking pada waktu {$booking->waktu_mulai}-{$booking->waktu_selesai}");
            }
        }
    }
    private function validatePicNoOverlap(array $data, int $userId, ?int $excludeBookingId = null): void
    {
        $userBookings = $this->bookingRepo->findUserBookingsOnDate(
            $userId,
            $data['tanggal_penggunaan_ruang'],
            $excludeBookingId
        );
        $date = $data['tanggal_penggunaan_ruang'];
        $s1 = Carbon::parse("$date {$data['waktu_mulai']}");
        $e1 = Carbon::parse("$date {$data['waktu_selesai']}");
        foreach ($userBookings as $booking) {
            $s2 = Carbon::parse("$date {$booking->waktu_mulai}");
            $e2 = Carbon::parse("$date {$booking->waktu_selesai}");
            if ($s1->lt($e2) && $e1->gt($s2)) {
                throw new Exception("Anda sudah menjadi PIC booking lain pada waktu {$booking->waktu_mulai}-{$booking->waktu_selesai}");
            }
        }
    }
    private function validateMemberNoOverlap(array $data, int $userId, ?int $excludeBookingId = null): void
    {
        $memberBookings = $this->bookingRepo->findUserMemberBookingsOnDate(
            $userId,
            $data['tanggal_penggunaan_ruang'],
            $excludeBookingId
        );
        $date = $data['tanggal_penggunaan_ruang'];
        $s1 = Carbon::parse("$date {$data['waktu_mulai']}");
        $e1 = Carbon::parse("$date {$data['waktu_selesai']}");
        foreach ($memberBookings as $booking) {
            $s2 = Carbon::parse("$date {$booking['waktu_mulai']}");
            $e2 = Carbon::parse("$date {$booking['waktu_selesai']}");
            if ($s1->lt($e2) && $e1->gt($s2)) {
                throw new Exception("Anda sudah menjadi anggota booking lain pada waktu {$booking['waktu_mulai']}-{$booking['waktu_selesai']}");
            }
        }
    }

    private function validateMinimumCapacity(int $bookingId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        $room = $this->bookingRepo->findByRoomId($booking->ruangan_id);

        if (!$room) {
            throw new Exception('Ruangan tidak ditemukan');
        }

        $memberCount = $this->bookingRepo->getMemberCount($bookingId);
        $totalParticipants = $memberCount + 1;

        if ($totalParticipants < $room['kapasitas_min']) {
            throw new Exception("Jumlah anggota minimal {$room['kapasitas_min']} orang (saat ini: {$totalParticipants})");
        }
    }

    private function validateMemberCanJoin(int $userId, int $bookingId): void
    {
        $member = $this->bookingRepo->findUserById($userId);

        if (!$member) {
            throw new Exception("User tidak ditemukan");
        }

        if ($member['status'] !== 'active') {
            throw new Exception('Member harus memiliki status active');
        }

        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        $startDateTime = Carbon::parse("{$booking->tanggal_penggunaan_ruang} {$booking->waktu_mulai}");
        $now = Carbon::now();

        if ($startDateTime->isFuture() && $now->diffInMinutes($startDateTime, false) < 15) {
            throw new Exception('Tidak dapat menambah anggota kurang dari 15 menit sebelum mulai');
        }

        $data = [
            'tanggal_penggunaan_ruang' => $booking->tanggal_penggunaan_ruang,
            'waktu_mulai' => $booking->waktu_mulai,
            'waktu_selesai' => $booking->waktu_selesai,
        ];

        $this->validateMemberNoOverlap($data, $userId, $bookingId);

        $room = $this->bookingRepo->findByRoomId($booking->ruangan_id);

        if ($room && $room['status_ruangan'] === 'adminOnly') {
            $isAdminOrDosen = $member['id_role'] === 1 || $member['id_role'] === 2 || $member['id_role'] === 4;
            if (!$isAdminOrDosen) {
                throw new Exception('Ruangan ini hanya dapat digunakan oleh Admin, Dosen, atau Tendik');
            }
        }
    }

    private function revalidateBookingForApproval(int $bookingId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        $data = [
            'ruangan_id' => $booking->ruangan_id,
            'tanggal_penggunaan_ruang' => $booking->tanggal_penggunaan_ruang,
            'waktu_mulai' => $booking->waktu_mulai,
            'waktu_selesai' => $booking->waktu_selesai,
            'tujuan' => $booking->tujuan,
        ];

        $this->validateNotPastBookings($data);
        $this->validateTimeOrder($data);
        $this->validateDuration($data);
        $this->validateSessionHours($data);
        $this->validateBreakTime($data);
        $this->validateMinLeadTime($data);
        $this->validateRoomAvailable($booking->ruangan_id);
        $this->validateNoTimeConflicts($data, $booking->user_id, $bookingId);
        $this->validateMinimumCapacity($bookingId);
    }

    private function validateHasPendingFeedback(int $userId): void
    {
        $pendingFeedbacks = $this->bookingRepo->getUserPendingFeedbacks($userId);

        if (!empty($pendingFeedbacks)) {
            throw new Exception('Anda memiliki feedback yang belum diisi. Harap isi feedback terlebih dahulu sebelum membuat booking baru.');
        }
    }

    public function deleteDraft(int $bookingId, int $userId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        if ((int) $booking->user_id !== $userId) {
            throw new Exception('Hanya PIC yang dapat menghapus draft');
        }

        if ($booking->status !== 'draft') {
            throw new Exception('Hanya booking dengan status draft yang dapat dihapus');
        }

        $this->bookingRepo->delete($bookingId);
        $this->logger->info('Draft Deleted', [
            'booking_id' => $bookingId,
            'deleted_by' => $userId,
        ]);
    }

    public function updateDraft(int $bookingId, array $data, int $userId): Booking
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        if ($booking->status !== 'draft') {
            throw new Exception('Hanya booking dengan status draft yang dapat diedit');
        }

        if ($booking->user_id !== $userId) {
            throw new Exception('Hanya PIC yang dapat mengedit booking ini');
        }

        if (isset($data['ruangan_id'])) {
            $booking->ruangan_id = $data['ruangan_id'];
        }

        if (isset($data['tanggal_penggunaan_ruang'])) {
            $booking->tanggal_penggunaan_ruang = $data['tanggal_penggunaan_ruang'];
        }

        if (isset($data['waktu_mulai'])) {
            $booking->waktu_mulai = $data['waktu_mulai'];
        }

        if (isset($data['waktu_selesai'])) {
            $booking->waktu_selesai = $data['waktu_selesai'];
        }

        if (isset($data['tujuan'])) {
            $booking->tujuan = $data['tujuan'];
        }

        $booking->save();
        $this->logger->info('User Updated Draft Booking', [
            'booking_id' => $bookingId,
            'user_id' => $userId,
            'updated_fields' => array_keys($data),
        ]);

        return $booking;
    }

    public function cancelPending(int $bookingId, int $userId): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        if ($booking->status !== 'pending') {
            throw new Exception('Hanya booking dengan status pending yang dapat dibatalkan');
        }

        if ($booking->user_id !== $userId) {
            throw new Exception('Hanya PIC yang dapat membatalkan booking ini');
        }

        $booking->status = 'draft';
        $booking->save();
        $this->logger->info('Pending Booking Reverted to Draft', [
            'booking_id' => $bookingId,
            'user_id' => $userId,
        ]);
    }

    public function sendInvitation(int $bookingId, int $invitedUserId, int $invitedByUserId): bool
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        if ($booking->status !== 'draft') {
            throw new Exception('Hanya booking draft yang dapat mengundang anggota');
        }

        if ((int) $booking->user_id !== $invitedByUserId) {
            throw new Exception('Hanya PIC yang dapat mengundang anggota');
        }

        if ($invitedUserId === $invitedByUserId) {
            throw new Exception('Tidak dapat mengundang diri sendiri');
        }

        $userPendingCount = $this->invitationRepo->countPendingForUser($invitedUserId);
        if ($userPendingCount >= 3) {
            throw new Exception('User sudah memiliki 3 undangan yang belum direspon');
        }

        if ($this->bookingRepo->isMemberOfBooking($bookingId, $invitedUserId)) {
            throw new Exception('User sudah menjadi anggota booking ini');
        }

        $existing = $this->invitationRepo->findByBookingAndUser($bookingId, $invitedUserId);

        // Case 1: Already invited by PIC
        if ($existing && $existing->status === 'pending' && $existing->invited_by_user_id !== null) {
            throw new Exception('Undangan sudah dikirim ke User ini');
        }

        // Case 2: User already requested via token - auto approve!
        if ($existing && $existing->status === 'pending' && $existing->invited_by_user_id === null) {
            $this->validateMemberCanJoin($invitedUserId, $bookingId);
            $this->invitationRepo->accept($existing->id_invitation);
            $this->bookingRepo->addMember($bookingId, $invitedUserId);
            $this->logger->info('PIC auto-approved user join request', [
                'booking_id' => $bookingId,
                'user_id' => $invitedUserId,
                'approved_by' => $invitedByUserId,
            ]);
            return true;
        }

        // Check pending limit
        $userPendingCount = $this->invitationRepo->countPendingForUser($invitedUserId);
        if ($userPendingCount >= 3) {
            throw new Exception('User sudah memiliki 3 undangan yang belum direspon');
        }

        // Check capacity
        $room = $this->bookingRepo->findByRoomId($booking->ruangan_id);
        if (!$room) {
            throw new Exception('Ruangan tidak ditemukan');
        }
        $currentMemberCount = $this->bookingRepo->getMemberCount($bookingId);
        $pendingCount = count($this->invitationRepo->getPendingForBooking($bookingId));
        $totalParticipants = $currentMemberCount + $pendingCount + 1 + 1;
        if ($totalParticipants > $room['kapasitas_max']) {
            throw new Exception("Kapasitas maksimal {$room['kapasitas_max']} orang sudah tercapai");
        }

        // Case 3: Old invitation exists - reset as PIC invite
        if ($existing) {
            $existing->status = 'pending';
            $existing->invited_by_user_id = $invitedByUserId;
            $existing->save();
        } else {
            // Case 4: No existing - create new PIC invitation
            $this->invitationRepo->create([
                'booking_id' => $bookingId,
                'invited_user_id' => $invitedUserId,
                'invited_by_user_id' => $invitedByUserId,
                'status' => 'pending',
            ]);
        }

        $this->logger->info('Invitation sent', [
            'booking_id' => $bookingId,
            'invited_user_id' => $invitedUserId,
            'invited_by_user_id' => $invitedByUserId,
        ]);

        return false;
    }

    public function acceptInvitation(int $invitationId, int $userId): int
    {
        $invitation = $this->invitationRepo->findById($invitationId);

        if (!$invitation) {
            throw new Exception('Undangan tidak ditemukan');
        }

        if ((int) $invitation->invited_user_id !== $userId) {
            throw new Exception('Undangan ini bukan untuk Anda');
        }

        if ($invitation->status !== 'pending') {
            throw new Exception('Undangan sudah diproses');
        }

        $booking = $this->bookingRepo->findById($invitation->booking_id);
        if (!$booking || $booking->status !== 'draft') {
            throw new Exception('Booking tidak ditemukan atau tidak dalam status draft');
        }

        $this->validateMemberCanJoin($userId, $booking->id_booking);
        $this->invitationRepo->accept($invitationId);
        $this->bookingRepo->addMember($invitation->booking_id, $userId);

        $this->logger->info('Invitation accepted', [
            'invitation_id' => $invitationId,
            'user_id' => $userId,
            'booking_id' => $invitation->booking_id,
        ]);

        return $invitation->booking_id;
    }

    public function rejectInvitation(int $invitationId, int $userId): void
    {
        $invitation = $this->invitationRepo->findById($invitationId);

        if (!$invitation) {
            throw new Exception('Undangan tidak ditemukan');
        }

        if ((int) $invitation->invited_user_id !== $userId) {
            throw new Exception('Undangan ini bukan untuk Anda');
        }

        if ($invitation->status !== 'pending') {
            throw new Exception('Undangan sudah diproses');
        }

        $this->invitationRepo->reject($invitationId);

        $this->logger->info('Invitation rejected', [
            'invitation_id' => $invitationId,
            'user_id' => $userId,
            'booking_id' => $invitation->booking_id,
        ]);
    }

    public function cancelInvitation(int $invitationId, int $picUserId): void
    {
        $invitation = $this->invitationRepo->findById($invitationId);

        if (!$invitation) {
            throw new Exception('Undangan tidak ditemukan');
        }

        if ((int) $invitation->invited_by_user_id !== $picUserId) {
            throw new Exception('Hanya PIC yang dapat membatalkan undangan');
        }

        if ($invitation->status !== 'pending') {
            throw new Exception('Undangan sudah diproses');
        }

        $booking = $this->bookingRepo->findById($invitation->booking_id);
        if ($booking && $booking->status !== 'draft') {
            throw new Exception('Tidak dapat membatalkan undangan - booking sudah diproses');
        }

        $this->invitationRepo->delete($invitationId);

        $this->logger->info('Invitation cancelled', [
            'invitation_id' => $invitationId,
            'pic_user_id' => $picUserId,
            'booking_id' => $invitation->booking_id,
        ]);
    }

    public function getPendingForUser(int $userId): array
    {
        return $this->invitationRepo->getPendingForUser($userId);
    }

    public function getPendingForBooking(int $bookingId): array
    {
        return $this->invitationRepo->getPendingForBooking($bookingId);
    }

    public function findUserByIdentifier(string $identifier): ?object
    {
        return $this->invitationRepo->findUserByIdentifier($identifier);
    }

    public function getPendingInvitedByPic(int $bookingId, int $picUserId): array
    {
        return $this->invitationRepo->getPendingInvitedByPic($bookingId, $picUserId);
    }
    public function getPendingJoinRequests(int $bookingId): array
    {
        return $this->invitationRepo->getPendingJoinRequests($bookingId);
    }

    public function approveJoinRequest(int $invitationId, int $picUserId): void
    {
        $invitation = $this->invitationRepo->findById($invitationId);

        if (!$invitation) {
            throw new Exception('Permintaan tidak ditemukan');
        }

        if ($invitation->invited_by_user_id !== null) {
            throw new Exception('Ini bukan permintaan bergabung');
        }

        $booking = $this->bookingRepo->findById($invitation->booking_id);

        if ($booking->status !== 'draft') {
            throw new Exception('Booking tidak dalam status draft');
        }

        if (!$booking || (int) $booking->user_id !== $picUserId) {
            throw new Exception('Hanya PIC yang dapat menyetujui permintaan');
        }


        if ($invitation->status !== 'pending') {
            throw new Exception('Permintaan sudah diproses');
        }

        $this->validateMemberCanJoin($invitation->invited_user_id, $booking->id_booking);
        $this->invitationRepo->accept($invitationId);
        $this->bookingRepo->addMember($invitation->booking_id, $invitation->invited_user_id);

        $this->logger->info('Join request approved', [
            'invitation_id' => $invitationId,
            'user_id' => $invitation->invited_user_id,
            'approved_by' => $picUserId,
        ]);
    }
    public function rejectJoinRequest(int $invitationId, int $picUserId): void
    {
        $invitation = $this->invitationRepo->findById($invitationId);

        if (!$invitation) {
            throw new Exception('Permintaan tidak ditemukan');
        }

        $booking = $this->bookingRepo->findById($invitation->booking_id);
        if (!$booking || (int) $booking->user_id !== $picUserId) {
            throw new Exception('Hanya PIC yang dapat menolak permintaan');
        }

        if ($invitation->status !== 'pending') {
            throw new Exception('Permintaan sudah diproses');
        }

        $this->invitationRepo->reject($invitationId);
        $this->logger->info('Join request rejected', [
            'invitation_id' => $invitationId,
            'rejected_by' => $picUserId,
        ]);
    }

    public function getMyPendingJoinRequests(int $userId): array
    {
        return $this->invitationRepo->getMyPendingJoinRequests($userId);
    }

    public function cancelJoinRequest(int $invitationId, int $userId): void
    {
        $invitation = $this->invitationRepo->findById($invitationId);

        if (!$invitation) {
            throw new Exception('Permintaan tidak ditemukan');
        }

        if ((int) $invitation->invited_user_id !== $userId) {
            throw new Exception('Ini bukan permintaan Anda');
        }

        if ($invitation->invited_by_user_id !== null) {
            throw new Exception('Ini bukan permintaan bergabung');
        }

        if ($invitation->status !== 'pending') {
            throw new Exception('Permintaan sudah diproses');
        }

        $this->invitationRepo->delete($invitationId);
        $this->logger->info('User cancelled their join request', [
            'invitation_id' => $invitationId,
            'user_id' => $userId,
        ]);
    }
}
