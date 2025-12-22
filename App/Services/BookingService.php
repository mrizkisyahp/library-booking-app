<?php

namespace App\Services;

use App\Repositories\BookingRepository;
use App\Repositories\InvitationRepository;
use App\Repositories\FeedbackRepository;
use App\Repositories\RescheduleRepository;
use App\Models\Booking;
use App\Models\User;
use App\Models\RescheduleRequest;
use Carbon\Carbon;
use Exception;
use App\Core\Paginator;
use App\Core\Database;


class BookingService
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
        private InvitationRepository $invitationRepo,
        private RescheduleRepository $rescheduleRepo,
        private Database $db,
        private EmailService $emailService,
        private ?SettingsService $settingsService = null
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

    public function getTodayBookings(array $filters = [], int $perPage = 15, int $page = 1): Paginator
    {
        return $this->bookingRepo->getTodayBookings($filters, $perPage, $page);
    }

    public function getStatusCounts(): array
    {
        $counts = $this->bookingRepo->getBookingCountByStatus();
        return [
            'pending' => $counts['pending'] ?? 0,
            'verified' => $counts['verified'] ?? 0,
            'active' => $counts['active'] ?? 0,
            'completed' => $counts['completed'] ?? 0,
            'cancelled' => $counts['cancelled'] ?? 0,
            'expired' => $counts['expired'] ?? 0,
            'no_show' => $counts['no_show'] ?? 0,
        ];
    }

    public function getUnreadCounts(array $lastViewed): array
    {
        $statuses = ['draft', 'pending', 'verified', 'active', 'completed', 'cancelled', 'expired', 'no_show'];
        $unread = [];

        foreach ($statuses as $status) {
            $timestamp = $lastViewed[$status] ?? '2000-01-01 00:00:00';
            $unread[$status] = $this->bookingRepo->getCountByStatusSince($status, $timestamp);
        }

        return $unread;
    }

    public function getTodayStatusCounts(): array
    {
        $counts = $this->bookingRepo->getTodayBookingCountByStatus();
        return [
            'pending' => $counts['pending'] ?? 0,
            'verified' => $counts['verified'] ?? 0,
            'active' => $counts['active'] ?? 0,
            'completed' => $counts['completed'] ?? 0,
            'cancelled' => $counts['cancelled'] ?? 0,
            'expired' => $counts['expired'] ?? 0,
            'no_show' => $counts['no_show'] ?? 0,
        ];
    }
    public function getBlockedDates(): array
    {
        return $this->bookingRepo->getBlockedDates();
    }

    public function hasPendingRescheduleRequest(int $bookingId): bool
    {
        return $this->rescheduleRepo->findPendingByBookingId($bookingId) !== null;
    }

    public function getAllRooms(): array
    {
        return $this->bookingRepo->getAllRooms();
    }

    public function findRoomById(int $roomId): ?\App\Models\Room
    {
        return $this->bookingRepo->findRoomById($roomId);
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
        $booking->surat_path = $data['surat_path'] ?? null;
        $booking->save();

        $this->logger->info('Booking Created', [
            'booking_id' => $booking->id_booking,
            'pic' => auth()->user()->nama,
            'status' => $booking->status,
        ]);

        return $booking;
    }

    public function getBookingForUser(int $bookingId, int $userId, bool $isAdmin = false, int $page = 1, int $perPage = 6): array
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

        // Fetch PIC details explicitly
        $pic = $this->bookingRepo->findUserById($booking->user_id);

        $allMembers = $this->bookingRepo->getBookingMembers($bookingId, $page, $perPage);

        $canSubmit = $bookings->status === 'draft' && ($bookings->required_members <= 0 || $bookings->current_members >= $bookings->required_members);

        return [
            'booking' => $bookings,
            'isPic' => $isPic,
            'isMember' => $isMember,
            'canSubmit' => $canSubmit,
            'allMembers' => $allMembers,
            'pic' => $pic,
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
        try {
            $this->db->beginTransaction();

            $booking = $this->bookingRepo->findByIdWithLock($bookingId);

            if (!$booking) {
                throw new Exception('Booking tidak ditemukan');
            }

            if ((int) $booking->user_id !== $userId) {
                throw new Exception('Hanya PIC yang dapat submit booking');
            }

            if ($booking->status !== 'draft') {
                throw new Exception('Hanya booking dengan status draft yang bisa di submit');
            }

            // Lock room to ensure capacity/status consistency during check? 
            // Capacity is on room.
            $this->bookingRepo->findByRoomIdWithLock($booking->ruangan_id);

            $this->validateMinimumCapacity($bookingId);

            $this->invitationRepo->rejectAllPendingForBooking($bookingId);

            $this->transitionTo($bookingId, 'pending', 'Submitted by PIC for approval');

            // Send notification
            $user = $this->bookingRepo->findUserById($userId);
            if ($user) {
                $bookingDetails = $this->bookingRepo->findByIdWithDetails($bookingId);
                if ($bookingDetails) {
                    $this->emailService->sendBookingSubmitted($this->hydrateUser($user), (object) $bookingDetails);
                }
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function addMember(int $bookingId, int $memberUserId, int $requestingUserId): void
    {
        try {
            $this->db->beginTransaction();

            $booking = $this->bookingRepo->findByIdWithLock($bookingId);

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
            $totalParticipants = $currentMemberCount + 1 + 1; // Existing members + New member + PIC

            if ($totalParticipants > $room['kapasitas_max']) {
                throw new Exception("Kapasitas maksimal {$room['kapasitas_max']} orang sudah tercapai");
            }

            $this->bookingRepo->addMember($bookingId, $memberUserId);

            $this->logger->info('Member added to booking', [
                'booking_id' => $bookingId,
                'member_user_id' => $memberUserId,
                'added_by' => $requestingUserId,
            ]);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
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
        try {
            $this->db->beginTransaction();

            $booking = $this->bookingRepo->findByInviteToken($token);

            if (!$booking) {
                throw new Exception('Kode undangan tidak valid');
            }

            // Lock the booking to prevent race conditions on capacity
            $lockedBooking = $this->bookingRepo->findByIdWithLock($booking->id_booking);

            if ($lockedBooking->status !== 'draft') {
                throw new Exception('Tidak dapat bergabung - booking sudah ' . $lockedBooking->status);
            }

            if ($userId === (int) $lockedBooking->user_id) {
                throw new Exception('Anda adalah PIC dari booking ini');
            }

            if ($this->bookingRepo->isMemberOfBooking($lockedBooking->id_booking, $userId)) {
                throw new Exception('Anda sudah terdaftar dalam booking ini');
            }

            $existing = $this->invitationRepo->findByBookingAndUser($lockedBooking->id_booking, $userId);

            // Case 1: Already requested via token (self-request pending)
            if ($existing && $existing->status === 'pending' && $existing->invited_by_user_id === null) {
                throw new Exception('Anda sudah mengajukan permintaan bergabung');
            }

            // Case 2: PIC already invited this user - auto accept!
            if ($existing && $existing->status === 'pending' && $existing->invited_by_user_id !== null) {
                $this->validateMemberCanJoin($userId, $lockedBooking->id_booking);
                $this->invitationRepo->accept($existing->id_invitation);
                $this->bookingRepo->addMember($lockedBooking->id_booking, $userId);

                // Send notification that they joined (accepted invitation)
                $user = $this->bookingRepo->findUserById($userId);
                $bookingDetails = $this->bookingRepo->findByIdWithDetails($lockedBooking->id_booking);
                if ($user && $bookingDetails) {
                    $this->emailService->sendJoinRequestApproved(
                        $this->hydrateUser($user),
                        (object) $bookingDetails
                    );
                }

                $this->logger->info('User auto-accepted PIC invitation via token', [
                    'booking_id' => $lockedBooking->id_booking,
                    'user_id' => $userId,
                ]);

                $this->db->commit();
                return ['booking_id' => $lockedBooking->id_booking, 'auto_joined' => true];
            }

            // Check pending limit
            $userPendingCount = $this->invitationRepo->countPendingForUser($userId);
            if ($userPendingCount >= 3) {
                throw new Exception('Anda sudah memiliki 3 undangan yang belum direspon');
            }

            // Check capacity
            $room = $this->bookingRepo->findByRoomId($lockedBooking->ruangan_id);
            if (!$room) {
                throw new Exception('Ruangan tidak ditemukan');
            }
            $currentMemberCount = $this->bookingRepo->getMemberCount($lockedBooking->id_booking);
            $pendingCount = count($this->invitationRepo->getPendingForBooking($lockedBooking->id_booking));
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
                    'booking_id' => $lockedBooking->id_booking,
                    'invited_user_id' => $userId,
                    'invited_by_user_id' => null,
                    'status' => 'pending',
                ]);
            }

            $this->logger->info('User requested to join via invite token', [
                'booking_id' => $lockedBooking->id_booking,
                'user_id' => $userId,
            ]);

            $this->db->commit();
            return ['booking_id' => $lockedBooking->id_booking, 'auto_joined' => false];

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
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
        try {
            $this->db->beginTransaction();

            $booking = $this->bookingRepo->findByIdWithLock($bookingId);

            if (!$booking) {
                throw new Exception('Booking tidak ditemukan');
            }

            if ($booking->status !== 'pending') {
                throw new Exception('Booking tidak dapat diapprove - status booking adalah ' . $booking->status);
            }

            // Lock the room to prevent race conditions (double booking)
            $this->bookingRepo->findByRoomIdWithLock($booking->ruangan_id);

            $this->revalidateBookingForApproval($bookingId);

            $this->transitionTo($bookingId, 'verified', 'Approved by admin');

            // Send notification
            $user = $this->bookingRepo->findUserById($booking->user_id);
            if ($user) {
                $userObj = $this->hydrateUser($user);
                $bookingDetails = $this->bookingRepo->findByIdWithDetails($bookingId);
                if ($bookingDetails) {
                    $this->emailService->sendBookingValidated($userObj, (object) $bookingDetails);
                }
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function rejectBooking(int $bookingId, string $reason = 'Rejected by admin'): void
    {
        try {
            $this->db->beginTransaction();

            $booking = $this->bookingRepo->findByIdWithLock($bookingId);

            if (!$booking) {
                throw new Exception('Booking tidak ditemukan');
            }

            if (!in_array($booking->status, ['pending', 'verified'])) {
                throw new Exception('Hanya booking dengan status pending atau verified yang dapat di reject');
            }

            $this->transitionTo($bookingId, 'cancelled', $reason);

            // Send notification
            $user = $this->bookingRepo->findUserById($booking->user_id);
            if ($user) {
                $userObj = $this->hydrateUser($user);
                $bookingDetails = $this->bookingRepo->findByIdWithDetails($bookingId);
                if ($bookingDetails) {
                    $this->emailService->sendBookingCancelled($userObj, (object) $bookingDetails, $reason);
                }
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function rejectPendingBooking(int $bookingId, string $reason = 'Rejected by admin'): void
    {
        try {
            $this->db->beginTransaction();

            $booking = $this->bookingRepo->findByIdWithLock($bookingId);

            if (!$booking) {
                throw new Exception('Booking tidak ditemukan');
            }

            if ($booking->status !== 'pending') {
                throw new Exception('Hanya booking dengan status pending yang dapat di-reject ke draft');
            }

            $this->transitionTo($bookingId, 'draft', $reason);

            // Send notification
            $user = $this->bookingRepo->findUserById($booking->user_id);
            if ($user) {
                $userObj = $this->hydrateUser($user);
                $bookingDetails = $this->bookingRepo->findByIdWithDetails($bookingId);
                if ($bookingDetails) {
                    $this->emailService->sendBookingRejectedToDraft($userObj, (object) $bookingDetails, $reason);
                }
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
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

        // Send notification to PIC about no-show penalty
        $user = $this->bookingRepo->findUserById($booking->user_id);
        if ($user) {
            $warningCount = $user['peringatan'] ?? 0;
            $this->emailService->sendWarningNotification(
                $user['email'],
                $user['nama'],
                'no_show',
                'Tidak check-in dalam 10 menit setelah waktu mulai',
                $warningCount
            );
        }
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

        // Send feedback request to PIC
        $user = $this->bookingRepo->findUserById($booking->user_id);
        if ($user) {
            $bookingDetails = $this->bookingRepo->findByIdWithDetails($bookingId);
            if ($bookingDetails) {
                $this->emailService->sendFeedbackRequest($this->hydrateUser($user), (object) $bookingDetails);
            }
        }
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

            // Send feedback request to PIC
            $user = $this->bookingRepo->findUserById($booking->user_id);
            if ($user) {
                $bookingDetails = $this->bookingRepo->findByIdWithDetails($bookingId);
                if ($bookingDetails) {
                    $this->emailService->sendFeedbackRequest($this->hydrateUser($user), (object) $bookingDetails);
                }
            }
        }
    }

    public function cancelBooking(int $bookingId, int $userId, string $reason = 'Cancelled by user'): void
    {
        try {
            $this->db->beginTransaction();

            $booking = $this->bookingRepo->findByIdWithLock($bookingId);

            if (!$booking) {
                throw new Exception('Booking tidak ditemukan');
            }

            $user = $this->bookingRepo->findUserById($userId);
            $isAdmin = $user && $user['id_role'] === 1;

            // Prevent cancellation if library is closed (users only, admins bypass)
            if (!$isAdmin && $this->bookingRepo->isLibraryClosedToday()) {
                $reason = $this->bookingRepo->getClosureReason(date('Y-m-d'));
                throw new Exception("Tidak dapat membatalkan booking: Perpustakaan sedang tutup. Alasan: $reason");
            }

            if (!$isAdmin && (int) $booking->user_id !== $userId) {
                throw new Exception('Hanya PIC atau Admin yang dapat membatalkan booking');
            }

            if (in_array($booking->status, ['completed', 'cancelled', 'no_show'])) {
                throw new Exception('Booking tidak dapat dibatalkan');
            }

            if ($booking->status === 'active') {
                throw new Exception('Booking yang sudah aktif tidak dapat dibatalkan');
            }

            // PIC cancelling verified booking = warning to PIC + ALL members
            if ($booking->status === 'verified' && !$isAdmin) {
                $startDateTime = Carbon::parse("{$booking->tanggal_penggunaan_ruang} {$booking->waktu_mulai}");
                $now = Carbon::now();

                $minutesUntilStart = $now->diffInMinutes($startDateTime, false);

                if ($minutesUntilStart < 15) {
                    throw new Exception('Tidak dapat membatalkan booking kurang dari 15 menit sebelum waktu mulai');
                }

                // Apply warning to PIC + ALL members
                $this->applyVerifiedCancellationPenalty($bookingId, $booking->user_id);
            }

            $this->transitionTo($bookingId, 'cancelled', $reason);

            // Send cancellation confirmation email
            $user = $this->bookingRepo->findUserById($booking->user_id);
            if ($user) {
                $userObj = $this->hydrateUser($user);
                $bookingDetails = $this->bookingRepo->findByIdWithDetails($bookingId);
                if ($bookingDetails) {
                    $this->emailService->sendBookingCancelled($userObj, (object) $bookingDetails, $reason);
                }
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
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

            // Notify PIC about draft expiration
            $user = $this->bookingRepo->findUserById($booking->user_id);
            if ($user) {
                $userObj = $this->hydrateUser($user);
                $bookingDetails = $this->bookingRepo->findByIdWithDetails($bookingId);
                if ($bookingDetails) {
                    $this->emailService->sendBookingCancelled(
                        $userObj,
                        (object) $bookingDetails,
                        'Draft booking expired - tidak di-submit dalam waktu 15 menit sebelum waktu mulai'
                    );
                }
            }
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

            // Notify PIC about pending expiration
            $user = $this->bookingRepo->findUserById($booking->user_id);
            if ($user) {
                $userObj = $this->hydrateUser($user);
                $bookingDetails = $this->bookingRepo->findByIdWithDetails($bookingId);
                if ($bookingDetails) {
                    $this->emailService->sendBookingCancelled(
                        $userObj,
                        (object) $bookingDetails,
                        'Booking dibatalkan otomatis - tidak diverifikasi admin dalam waktu 5 menit sebelum waktu mulai'
                    );
                }
            }
        }
    }

    public function rescheduleBooking(int $bookingId, array $newData, int $userId): void
    {
        try {
            $this->db->beginTransaction();

            $booking = $this->bookingRepo->findByIdWithLock($bookingId);

            if (!$booking) {
                throw new Exception('Booking tidak ditemukan');
            }

            $user = $this->bookingRepo->findUserById($userId);
            $isAdmin = $user && $user['id_role'] === 1;

            // Prevent reschedule if library is closed (users only, admins bypass)
            if (!$isAdmin && $this->bookingRepo->isLibraryClosedToday()) {
                $reason = $this->bookingRepo->getClosureReason(date('Y-m-d'));
                throw new Exception("Tidak dapat reschedule: Perpustakaan sedang tutup. Alasan: $reason");
            }

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

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function createRescheduleRequest(int $bookingId, array $newData, int $userId): RescheduleRequest
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        if ((int) $booking->user_id !== $userId) {
            throw new Exception('Hanya PIC yang bisa mengajukan reschedule');
        }

        if ($booking->status !== 'verified') {
            throw new Exception('Hanya booking dengan status verified yang dapat di reschedule');
        }

        $existingRequest = $this->rescheduleRepo->findPendingByBookingId($bookingId);

        if ($existingRequest) {
            throw new Exception('Sudah ada permintaan reschedule yang menunggu persetujuan');
        }

        if ($booking->has_been_rescheduled ?? false) {
            throw new Exception('Booking hanya dapat di reschedule 1 kali');
        }

        $startDateTime = Carbon::parse("{$booking->tanggal_penggunaan_ruang} {$booking->waktu_mulai}");
        $now = Carbon::now();

        if ($now->gte($startDateTime)) {
            throw new Exception("Tidak dapat reschedule booking yang sudah dimulai");
        }

        if ($now->diffInMinutes($startDateTime, false) < 15) {
            throw new Exception('Tidak dapat reschedule kurang dari 15 menit sebelum waktu mulai');
        }

        $validationData = array_merge([
            'ruangan_id' => $booking->ruangan_id,
            'tujuan' => $booking->tujuan,
        ], $newData);

        $this->validateRescheduleRules($validationData);
        $this->validateNoTimeConflicts($validationData, $booking->user_id, $bookingId);

        $request = $this->rescheduleRepo->create([
            'booking_id' => $bookingId,
            'requested_tanggal' => $newData['tanggal_penggunaan_ruang'],
            'requested_waktu_mulai' => $newData['waktu_mulai'],
            'requested_waktu_selesai' => $newData['waktu_selesai'],
            'requested_by' => $userId,
            'status' => 'pending',
        ]);

        $this->logger->info('Reschedule Request Created', [
            'booking_id' => $bookingId,
            'request_id' => $request->id_request,
            'requested_by' => $userId,
        ]);
        return $request;
    }

    public function approveRescheduleRequest(int $requestId, int $adminId): void
    {
        $request = $this->rescheduleRepo->findById($requestId);

        if (!$request) {
            throw new Exception('Permintaan reschedule tidak ditemukan');
        }

        if ($request->status !== 'pending') {
            throw new Exception('Permintaan sudah di proses');
        }

        $booking = $this->bookingRepo->findById($request->booking_id);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        $booking->tanggal_penggunaan_ruang = $request->requested_tanggal;
        $booking->waktu_mulai = $request->requested_waktu_mulai;
        $booking->waktu_selesai = $request->requested_waktu_selesai;
        $booking->has_been_rescheduled = true;
        $booking->status = 'pending';
        $booking->checkin_code = null;
        $booking->save();

        $this->rescheduleRepo->approve($requestId, $adminId);

        $this->logger->info('Reschedule Request Approved', [
            'request_id' => $requestId,
            'booking_id' => $request->booking_id,
            'approved_by' => $adminId,
        ]);

        // Send Notification
        $user = $this->bookingRepo->findUserById($booking->user_id);
        if ($user) {
            $this->emailService->sendRescheduleApproved($this->hydrateUser($user), (object) $booking);
        }
    }

    public function rejectRescheduleRequest(int $requestId, int $adminId, string $reason): void
    {
        $request = $this->rescheduleRepo->findById($requestId);

        if (!$request) {
            throw new Exception('Permintaan reschedule tidak ditemukan');
        }

        if ($request->status !== 'pending') {
            throw new Exception('Permintaan sudah diproses');
        }

        $this->rescheduleRepo->reject($requestId, $adminId, $reason);

        $this->logger->info('Reschedule Request Rejected', [
            'request_id' => $requestId,
            'booking_id' => $request->booking_id,
            'rejected_by' => $adminId,
            'reason' => $reason,
        ]);

        // Send Notification
        $booking = $this->bookingRepo->findById($request->booking_id);
        if ($booking) {
            $user = $this->bookingRepo->findUserById($booking->user_id);
            if ($user) {
                $this->emailService->sendRescheduleRejected($this->hydrateUser($user), (object) $booking, $reason);
            }
        }
    }

    public function getPendingRescheduleRequest(int $bookingId): ?RescheduleRequest
    {
        return $this->rescheduleRepo->findPendingByBookingId($bookingId);
    }

    public function getAllPendingRescheduleRequests(): array
    {
        return $this->rescheduleRepo->getAllPending();
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

    /**
     * Apply "PIC Batalkan Booking Verified" warning to PIC + ALL members
     */
    public function applyVerifiedCancellationPenalty(int $bookingId, int $picUserId): void
    {
        // Get warning type ID and name
        $stmt = $this->db->prepare("
            SELECT id_peringatan, nama_peringatan FROM peringatan_suspensi 
            WHERE nama_peringatan LIKE '%Batalkan%Verified%' 
            AND deleted_at IS NULL 
            LIMIT 1
        ");
        $stmt->execute();
        $warningType = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$warningType) {
            // Fallback: try to get any warning type
            $stmt = $this->db->pdo->query("SELECT id_peringatan, nama_peringatan FROM peringatan_suspensi WHERE deleted_at IS NULL LIMIT 1");
            $warningType = $stmt->fetch(\PDO::FETCH_ASSOC);
        }

        if (!$warningType) {
            $this->logger->error('No warning type found for verified cancellation penalty');
            return;
        }

        $peringatanId = $warningType['id_peringatan'];
        $warningTypeName = $warningType['nama_peringatan'] ?? 'PIC Batalkan Booking Verified';

        // Get all members + PIC
        $members = $this->bookingRepo->getBookingMembers($bookingId, 1, 999);
        $allUserIds = array_column($members->items, 'id_user');
        $allUserIds[] = $picUserId;
        $allUserIds = array_unique($allUserIds);

        foreach ($allUserIds as $userId) {
            try {
                // Insert warning into peringatan_mhs
                $stmt = $this->db->prepare("
                    INSERT INTO peringatan_mhs (id_peringatan, id_akun, tgl_peringatan, created_at)
                    VALUES (:id_peringatan, :id_akun, :tgl, NOW())
                ");
                $stmt->execute([
                    ':id_peringatan' => $peringatanId,
                    ':id_akun' => $userId,
                    ':tgl' => date('Y-m-d'),
                ]);

                // Update users.peringatan
                $user = $this->bookingRepo->findUserById($userId);
                if ($user) {
                    $newWarningLevel = ($user['peringatan'] ?? 0) + 1;
                    $this->bookingRepo->updateUserWarning($userId, $newWarningLevel);

                    $this->logger->warning('Verified Cancellation Penalty Applied', [
                        'booking_id' => $bookingId,
                        'user_id' => $userId,
                        'new_warning_level' => $newWarningLevel,
                    ]);

                    // Send warning email
                    try {
                        $this->emailService->sendWarningNotification(
                            $user['email'],
                            $user['nama'],
                            $warningTypeName,
                            "Booking #{$bookingId} dibatalkan oleh PIC",
                            $newWarningLevel
                        );
                    } catch (\Exception $e) {
                        $this->logger->error('Failed to send warning email', [
                            'user_id' => $userId,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    // Auto-suspend if >= 3
                    if ($newWarningLevel >= 3 && $user['status'] !== 'suspended') {
                        $suspendUntil = Carbon::now()->addDays(7)->format('Y-m-d');
                        $this->bookingRepo->updateUserStatus($userId, 'suspended', $suspendUntil);

                        // Insert suspension record
                        $stmt = $this->db->prepare("
                            INSERT INTO suspensi (id_akun, tgl_suspensi, created_at)
                            VALUES (:id_akun, :tgl, NOW())
                        ");
                        $stmt->execute([':id_akun' => $userId, ':tgl' => date('Y-m-d')]);

                        $this->logger->warning('User Auto-Suspended from verified cancellation', [
                            'user_id' => $userId,
                            'warning_level' => $newWarningLevel,
                        ]);

                        // Send suspension email
                        try {
                            $this->emailService->sendSuspensionNotification(
                                $user['email'],
                                $user['nama'],
                                $suspendUntil
                            );
                        } catch (\Exception $e) {
                            $this->logger->error('Failed to send suspension email', [
                                'user_id' => $userId,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->logger->error('Failed to apply verified cancellation penalty', [
                    'booking_id' => $bookingId,
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);
            }
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

    /**
     * Block date range for multiple rooms
     * @param array $ruanganIds Array of room IDs (empty = all rooms)
     */
    public function blockDateRange(string $dateBegin, string $dateEnd, array $ruanganIds, string $reason, int $adminId): void
    {
        $begin = Carbon::parse($dateBegin);
        $end = Carbon::parse($dateEnd);

        if ($end->lt($begin)) {
            throw new Exception('Tanggal akhir harus setelah tanggal awal');
        }

        $this->bookingRepo->blockDateRange($begin->format('Y-m-d'), $end->format('Y-m-d'), $ruanganIds, $reason, $adminId);

        $this->logger->info('Date Range Blocked', [
            'date_begin' => $begin->format('Y-m-d'),
            'date_end' => $end->format('Y-m-d'),
            'ruangan_ids' => $ruanganIds,
            'reason' => $reason,
            'blocked_by' => $adminId,
        ]);
    }

    /**
     * Get affected bookings by date range and room IDs
     */
    public function getAffectedBookings(string $dateBegin, string $dateEnd, array $ruanganIds = []): array
    {
        return $this->bookingRepo->findAffectedBookingsByDateRange($dateBegin, $dateEnd, $ruanganIds);
    }

    /**
     * Block date range and cancel affected bookings
     */
    public function blockDateRangeWithCancellation(string $dateBegin, string $dateEnd, array $ruanganIds, string $reason, int $adminId): array
    {
        $begin = Carbon::parse($dateBegin);
        $end = Carbon::parse($dateEnd);

        if ($end->lt($begin)) {
            throw new Exception('Tanggal akhir harus setelah tanggal awal');
        }

        // Get affected bookings before blocking
        $affectedBookings = $this->bookingRepo->findAffectedBookingsByDateRange(
            $begin->format('Y-m-d'),
            $end->format('Y-m-d'),
            $ruanganIds
        );

        // Block the dates
        $this->blockDateRange($dateBegin, $dateEnd, $ruanganIds, $reason, $adminId);

        // Separate drafts from other bookings
        $draftCount = 0;
        $cancelledCount = 0;

        foreach ($affectedBookings as $booking) {
            // If draft: hard delete (no email)
            if ($booking['status'] === 'draft') {
                $this->bookingRepo->delete($booking['id_booking']);
                $draftCount++;

                $this->logger->info('Draft booking deleted due to date block', [
                    'booking_id' => $booking['id_booking'],
                    'user_id' => $booking['user_id'],
                    'date' => $booking['tanggal_penggunaan_ruang']
                ]);
                continue;
            }

            // For other statuses: cancel and send email
            $this->transitionTo($booking['id_booking'], 'cancelled', "Ruangan diblokir: $reason");
            $cancelledCount++;

            // Get full User object for email
            $user = User::Query()->where('id_user', $booking['user_id'])->first();
            if ($user) {
                // Send cancellation email
                try {
                    // Create booking object from array for email template
                    $bookingObj = (object) [
                        'booking_code' => 'N/A',
                        'booking_date' => date('d M Y', strtotime($booking['tanggal_penggunaan_ruang'])),
                        'start_time' => substr($booking['waktu_mulai'], 0, 5),
                        'end_time' => substr($booking['waktu_selesai'], 0, 5),
                    ];

                    $this->emailService->sendBookingCancelled(
                        $user,
                        $bookingObj,
                        "Ruangan {$booking['nama_ruangan']} diblokir pada periode " . $begin->format('d M Y') . " - " . $end->format('d M Y') . ". Alasan: $reason"
                    );

                    $this->logger->info('Cancellation email sent', [
                        'booking_id' => $booking['id_booking'],
                        'email' => $user->email
                    ]);
                } catch (Exception $e) {
                    $this->logger->error('Failed to send cancellation email', [
                        'booking_id' => $booking['id_booking'],
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        // Log summary
        $this->logger->info('Date range blocked with booking cleanup', [
            'date_begin' => $dateBegin,
            'date_end' => $dateEnd,
            'total_affected' => count($affectedBookings),
            'drafts_deleted' => $draftCount,
            'bookings_cancelled' => $cancelledCount
        ]);

        return $affectedBookings;
    }

    public function unblockDate(int $blockedDateId): void
    {
        $this->bookingRepo->unblockDate($blockedDateId);
        $this->logger->info('Date Unblocked', ['blocked_date_id' => $blockedDateId]);
    }

    /**
     * Reopen library by removing all blocks that cover today
     * @return int Number of blocks removed
     */
    public function reopenLibraryToday(): int
    {
        $blockedDates = $this->getBlockedDates();
        $today = date('Y-m-d');
        $unblockedCount = 0;

        foreach ($blockedDates as $block) {
            if ($block['tanggal_begin'] <= $today && $block['tanggal_end'] >= $today) {
                $this->unblockDate($block['id_blocked_date']);
                $unblockedCount++;
            }
        }

        $this->logger->info('Library Reopened Today', ['unblocked_count' => $unblockedCount]);
        return $unblockedCount;
    }

    /**
     * Delete all blocked dates
     * @return int Number of blocks deleted
     */
    public function deleteAllBlockedDates(): int
    {
        $blockedDates = $this->getBlockedDates();
        $count = 0;

        foreach ($blockedDates as $block) {
            $this->unblockDate($block['id_blocked_date']);
            $count++;
        }

        $this->logger->info('All Blocked Dates Deleted', ['count' => $count]);
        return $count;
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

        $minDuration = $this->settingsService?->get('min_booking_duration') ?? 60;
        $maxDuration = $this->settingsService?->get('max_booking_duration') ?? 180;

        if ($durationMinutes < $minDuration) {
            $minText = $minDuration >= 60 ? ($minDuration / 60) . " jam" : $minDuration . " menit";
            throw new Exception("Durasi booking minimal {$minText}");
        }

        if ($durationMinutes > $maxDuration) {
            $maxText = $maxDuration >= 60 ? ($maxDuration / 60) . " jam" : $maxDuration . " menit";
            throw new Exception("Durasi booking maksimal {$maxText}");
        }
    }

    private function validateSessionHours(array $data): void
    {
        $dateStr = $data['tanggal_penggunaan_ruang'];
        $startDateTime = Carbon::parse("$dateStr {$data['waktu_mulai']}");
        $endDateTime = Carbon::parse("$dateStr {$data['waktu_selesai']}");

        // Get operating hours from settings or use defaults
        $openTime = $this->settingsService?->get('library_open_time') ?? '08:15';
        $closeTime = $this->settingsService?->get('library_close_time') ?? '16:00';

        $operatingStart = Carbon::parse("$dateStr $openTime");
        $operatingEnd = Carbon::parse("$dateStr $closeTime");

        if ($startDateTime->lt($operatingStart)) {
            throw new Exception("Booking tidak bisa dimulai sebelum jam $openTime");
        }

        if ($endDateTime->gt($operatingEnd)) {
            throw new Exception("Booking harus selesai sebelum jam $closeTime");
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
            $breakStartTime = $this->settingsService?->get('break_start_friday') ?? '11:00';
            $breakEndTime = $this->settingsService?->get('break_end_friday') ?? '13:00';
            $breakStart = Carbon::parse("$dateStr $breakStartTime");
            $breakEnd = Carbon::parse("$dateStr $breakEndTime");

            if ($startDateTime->lt($breakEnd) && $endDateTime->gt($breakStart)) {
                throw new Exception("Booking tidak boleh melewati jam istirahat Jumat ($breakStartTime-$breakEndTime)");
            }
        } else { // Senin - Kamis
            $breakStartTime = $this->settingsService?->get('break_start_weekday') ?? '11:00';
            $breakEndTime = $this->settingsService?->get('break_end_weekday') ?? '12:00';
            $breakStart = Carbon::parse("$dateStr $breakStartTime");
            $breakEnd = Carbon::parse("$dateStr $breakEndTime");

            if ($startDateTime->lt($breakEnd) && $endDateTime->gt($breakStart)) {
                throw new Exception("Booking tidak boleh melewati jam istirahat ($breakStartTime-$breakEndTime)");
            }
        }
    }

    private function validateMaxDaysAhead(array $data): void
    {
        $bookingDate = Carbon::parse($data['tanggal_penggunaan_ruang']);
        $today = Carbon::today();

        // Get active operating days from system settings
        $activeDays = $this->settingsService?->getActiveDays() ?? [1, 2, 3, 4, 5]; // Default Mon-Fri

        $maxDate = $today->copy();
        $workingDays = 0;
        while ($workingDays < 7) {
            $maxDate->addDay();
            // Check if this day is an active operating day
            if (in_array($maxDate->dayOfWeek, $activeDays)) {
                $workingDays++;
            }
        }
        if ($bookingDate->gt($maxDate)) {
            throw new Exception('Booking hanya bisa dibuat untuk 7 hari kerja ke depan');
        }
        // Check if booking date is on an operating day
        if (!in_array($bookingDate->dayOfWeek, $activeDays)) {
            throw new Exception('Booking tidak tersedia pada hari ini (bukan hari operasional)');
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
        // Check if user is PIC of any booking on this date
        $existingPicBookings = $this->bookingRepo->findUserBookingsOnDate($userId, $date);
        if (count($existingPicBookings) > 0) {
            throw new Exception('Anda hanya dapat melakukan 1 booking per hari');
        }

        // Check if user is member of any booking on this date
        $existingMemberBookings = $this->bookingRepo->findUserMemberBookingsOnDate($userId, $date);
        if (count($existingMemberBookings) > 0) {
            throw new Exception('Anda sudah menjadi anggota booking lain pada tanggal ini');
        }
    }

    /**
     * Validate that a user can only join/be member of 1 booking per day
     * This is called when a member is trying to join a booking
     */
    private function validateMemberOneBookingPerDay(int $userId, string $date, int $excludeBookingId): void
    {
        // Check if user is PIC of any booking on this date
        $existingPicBookings = $this->bookingRepo->findUserBookingsOnDate($userId, $date, $excludeBookingId);
        if (count($existingPicBookings) > 0) {
            throw new Exception('Anda sudah memiliki booking pada tanggal ini');
        }

        // Check if user is member of any other booking on this date
        $existingMemberBookings = $this->bookingRepo->findUserMemberBookingsOnDate($userId, $date, $excludeBookingId);
        if (count($existingMemberBookings) > 0) {
            throw new Exception('Anda sudah menjadi anggota booking lain pada tanggal ini');
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

        // Check 1 booking per day rule for member
        $this->validateMemberOneBookingPerDay($userId, $booking->tanggal_penggunaan_ruang, $bookingId);

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

            // Send Email
            $invitedUser = $this->bookingRepo->findUserById($invitedUserId);
            $inviter = $this->bookingRepo->findUserById($invitedByUserId);
            $bookingDetails = $this->bookingRepo->findByIdWithDetails($bookingId);

            if ($invitedUser && $inviter && $bookingDetails) {
                $this->emailService->sendInvitation(
                    $this->hydrateUser($invitedUser),
                    (object) $bookingDetails,
                    $this->hydrateUser($inviter)
                );
            }
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

    private function hydrateUser(array $data): User
    {
        $user = new User();
        foreach ($data as $key => $value) {
            if (property_exists($user, $key)) {
                $user->$key = $value;
            }
        }
        return $user;
    }

    /**
     * Get all warning types for dropdowns
     */
    public function getWarningTypes(): array
    {
        $stmt = $this->db->pdo->query("
            SELECT id_peringatan, nama_peringatan 
            FROM peringatan_suspensi 
            WHERE deleted_at IS NULL 
            ORDER BY id_peringatan
        ");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Assign warnings to all members of a booking
     */
    public function assignWarningsToBooking(int $bookingId, int $warningTypeId, ?string $reason = null): array
    {
        $booking = $this->bookingRepo->findByIdWithDetails($bookingId);
        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        // Get all members (including PIC) via direct SQL
        $stmt = $this->db->prepare("
            SELECT DISTINCT u.id_user, u.nama, u.email, u.peringatan, u.status
            FROM users u
            LEFT JOIN anggota_booking ab ON u.id_user = ab.user_id
            WHERE ab.booking_id = :booking_id1
               OR u.id_user = :pic_id
        ");
        $stmt->execute([
            ':booking_id1' => $bookingId,
            ':pic_id' => $booking->id_user,
        ]);
        $members = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($members)) {
            throw new Exception('Tidak ada anggota dalam booking ini');
        }

        // Get warning type name
        $warningTypes = $this->getWarningTypes();
        $warningName = 'Peringatan';
        foreach ($warningTypes as $type) {
            if ((int) $type['id_peringatan'] === $warningTypeId) {
                $warningName = $type['nama_peringatan'];
                break;
            }
        }

        $warnedUsers = [];

        foreach ($members as $member) {
            $userId = (int) $member['id_user'];

            // Get fresh user data
            $userStmt = $this->db->prepare("SELECT * FROM users WHERE id_user = :id");
            $userStmt->execute([':id' => $userId]);
            $userData = $userStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$userData)
                continue;

            $user = $this->hydrateUser($userData);

            // Insert warning
            $insertStmt = $this->db->prepare("
                INSERT INTO peringatan_mhs (id_akun, id_peringatan, tgl_peringatan, created_at)
                VALUES (:id_akun, :id_peringatan, CURDATE(), NOW())
            ");
            $insertStmt->execute([
                ':id_akun' => $userId,
                ':id_peringatan' => $warningTypeId,
            ]);

            // Increment user warning count
            $newWarningCount = ($user->peringatan ?? 0) + 1;
            $user->peringatan = $newWarningCount;
            $user->save();

            // Send warning email
            $this->emailService->sendWarningNotification($user->email, $user->nama, $warningName, '', $newWarningCount);

            // Check for auto-suspension (3+ warnings)
            if ($newWarningCount >= 3 && $user->status !== 'suspended') {
                $suspensionEndDate = Carbon::now()->addDays(14)->format('Y-m-d');

                $suspendStmt = $this->db->prepare("
                    INSERT INTO suspensi (id_akun, tgl_suspensi, created_at)
                    VALUES (:id_akun, CURDATE(), NOW())
                ");
                $suspendStmt->execute([':id_akun' => $userId]);

                $user->status = 'suspended';
                $user->suspensi_terakhir = $suspensionEndDate;
                $user->save();

                $this->emailService->sendSuspensionNotification($user->email, $user->nama, $suspensionEndDate);
            }

            $warnedUsers[] = $user->nama;
        }

        $this->logger->info('Admin assigned warnings to booking members', [
            'booking_id' => $bookingId,
            'warning_type_id' => $warningTypeId,
            'reason' => $reason,
            'warned_users' => $warnedUsers,
        ]);

        return $warnedUsers;
    }
}