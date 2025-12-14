<?php

namespace App\Repositories;

use App\Core\QueryBuilder;
use App\Models\Feedback;
use App\Models\Booking;
use App\Core\Database;
use App\Core\Paginator;
use App\Models\Room;
use App\Models\User;

class BookingRepository
{
    public function __construct(private Database $database)
    {
    }

    public function findById(int $id): ?Booking
    {
        return Booking::Query()->where('id_booking', $id)->first();
    }

    public function findByIdWithLock(int $id): ?Booking
    {
        return Booking::Query()->lockForUpdate()->where('id_booking', $id)->first();
    }

    public function findByIdWithDetails(int $id): ?Booking
    {
        return $this->baseBookingQuery()
            ->where('b.id_booking', $id)
            ->first();
    }

    public function findByInviteToken(string $token): ?Booking
    {
        return Booking::Query()->where('invite_token', $token)->first();
    }

    public function findByRoomId(int $roomId): ?array
    {
        $qb = new QueryBuilder($this->database->pdo);
        return $qb->table('ruangan')->where('id_ruangan', $roomId)->first();
    }

    public function findByRoomIdWithLock(int $roomId): ?array
    {
        $qb = new QueryBuilder($this->database->pdo);
        return $qb->table('ruangan')->where('id_ruangan', $roomId)->lockForUpdate()->first();
    }

    public function getUserActiveBookings(int $userId, int $limit = 1): array
    {
        return $this->baseBookingQuery()
            ->leftJoin('anggota_booking ab', 'b.id_booking', '=', 'ab.booking_id')
            ->whereRaw('(b.user_id = ? OR ab.user_id = ?)', [$userId, $userId])
            ->whereRaw('NOT (b.status = ? AND f.id_feedback IS NOT NULL)', ['completed'])
            ->whereIn('b.status', ['draft', 'pending', 'verified', 'active', 'completed'])
            ->groupBy('b.id_booking')
            ->orderBy('b.tanggal_penggunaan_ruang', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getUserPendingFeedbacks(int $userId): array
    {
        return $this->baseBookingQuery()
            ->where('b.user_id', $userId)
            ->where('b.status', 'completed')
            ->whereNull('f.id_feedback')
            ->get();
    }

    public function getTotalBookings(): int
    {
        return Booking::Query()->count();
    }

    public function getBookingCountByStatus(): array
    {
        $qb = new QueryBuilder($this->database->pdo);
        $results = $qb->table('booking')
            ->select(['status', 'COUNT(*) as count'])
            ->groupBy('status')
            ->get();
        $counts = [];
        foreach ($results as $row) {
            $counts[$row['status']] = (int) $row['count'];
        }
        return $counts;
    }

    public function getTodayBookingCountByStatus(): array
    {
        $today = date('Y-m-d');
        $qb = new QueryBuilder($this->database->pdo);
        $results = $qb->table('booking')
            ->select(['status', 'COUNT(*) as count'])
            ->whereRaw("DATE(created_at) = ?", [$today])
            ->groupBy('status')
            ->get();

        $counts = [];
        foreach ($results as $row) {
            $counts[$row['status']] = (int) $row['count'];
        }
        return $counts;
    }

    public function getCountByStatusSince(string $status, string $timestamp): int
    {
        return Booking::Query()
            ->where('status', $status)
            ->where('created_at', '>', $timestamp)
            ->count();
    }

    public function getTodayBookings(array $filters = [], int $perPage = 15, int $page = 1): Paginator
    {
        $today = date('Y-m-d');
        $query = $this->baseBookingQuery()
            ->whereRaw("DATE(b.created_at) = ?", [$today]);

        if (!empty($filters['status'])) {
            $query->where('b.status', $filters['status']);
        }

        if (!empty($filters['keyword'])) {
            $query->where('r.nama_ruangan', 'LIKE', '%' . $filters['keyword'] . '%')
                ->orWhere('u.nama', 'LIKE', '%' . $filters['keyword'] . '%');
        }

        return $query
            ->whereNotIn('b.status', ['draft'])
            ->orderBy("CASE b.status 
                WHEN 'pending' THEN 1 
                WHEN 'verified' THEN 2 
                WHEN 'active' THEN 3 
                WHEN 'completed' THEN 4 
                WHEN 'cancelled' THEN 5 
                WHEN 'expired' THEN 6 
                WHEN 'no_show' THEN 7 
                ELSE 8 
            END", 'asc')
            ->orderBy('b.waktu_mulai', 'asc')
            ->paginate($perPage, $page);
    }

    public function getRecentBookings(int $limit = 10): array
    {
        return $this->baseBookingQuery()
            ->orderBy('b.tanggal_penggunaan_ruang', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getRoomUsageStatistics(): array
    {
        $qb = new QueryBuilder($this->database->pdo);
        return $qb->table('booking b')
            ->select(['r.nama_ruangan', 'COUNT(b.id_booking) as usage_count'])
            ->join('ruangan r', 'b.ruangan_id', '=', 'r.id_ruangan')
            ->whereIn('b.status', ['completed', 'active', 'verified'])
            ->groupBy('r.id_ruangan')
            ->orderBy('usage_count', 'desc')
            ->get();
    }

    public function getAllBookings(array $filters = [], int $perPage = 15, int $page = 1): Paginator
    {
        $query = $this->baseBookingQuery();
        if (!empty($filters['status'])) {
            $query->where('b.status', $filters['status']);
        }
        if (!empty($filters['keyword'])) {
            $query->where('r.nama_ruangan', 'LIKE', '%' . $filters['keyword'] . '%')
                ->orWhere('u.nama', 'LIKE', '%' . $filters['keyword'] . '%');
        }
        return $query
            ->whereNotIn('b.status', ['draft'])
            ->orderBy('b.tanggal_penggunaan_ruang', 'desc')
            ->orderBy("CASE b.status 
                WHEN 'pending' THEN 1 
                WHEN 'verified' THEN 2 
                WHEN 'active' THEN 3 
                WHEN 'completed' THEN 4 
                WHEN 'draft' THEN 5 
                WHEN 'cancelled' THEN 6 
                WHEN 'expired' THEN 7 
                WHEN 'no_show' THEN 8 
                ELSE 9 
            END", 'asc')
            ->orderBy('b.created_at', 'asc')
            ->paginate($perPage, $page);
    }

    public function getUserBookings(int $userId, array $filters = [], int $perPage = 15, int $page = 1): Paginator
    {
        $query = $this->baseBookingQuery()
            ->leftJoin('anggota_booking ab', 'b.id_booking', '=', 'ab.booking_id')
            ->whereRaw('(b.user_id = ? OR ab.user_id = ?)', [$userId, $userId])
            ->groupBy('b.id_booking');

        if (!empty($filters['nama_ruangan'])) {
            $query->where('r.nama_ruangan', 'LIKE', '%' . $filters['nama_ruangan'] . '%');
        }

        if (!empty($filters['tanggal'])) {
            $query->where('b.tanggal_penggunaan_ruang', $filters['tanggal']);
        }

        if (!empty($filters['jenis_ruangan'])) {
            $query->whereIn('r.jenis_ruangan', $filters['jenis_ruangan']);
        }

        if (!empty($filters['waktu_mulai'])) {
            $query->where('b.waktu_mulai', '>=', $filters['waktu_mulai']);
        }
        if (!empty($filters['kapasitas_min'])) {
            $query->where('r.kapasitas_max', '>=', (int) $filters['kapasitas_min']);
        }

        return $query->orderBy('b.tanggal_penggunaan_ruang', 'desc')
            ->paginate($perPage, $page);
    }

    public function getBookingMembers(int $bookingId, int $page = 1, int $perPage = 6): Paginator
    {
        $qb = new QueryBuilder($this->database->pdo);

        // Get booking to find PIC
        $booking = $this->findById($bookingId);
        if (!$booking) {
            return new Paginator([], 0, $perPage, 1, 0);
        }

        $picUserId = $booking->user_id;

        $unionSql = "
        SELECT u.id_user, u.nama, u.email, u.nim, u.nip, u.kubaca_img,
               1 as is_owner, 0 as sort_order
        FROM users u WHERE u.id_user = ?
        UNION ALL
        SELECT u.id_user, u.nama, u.email, u.nim, u.nip, u.kubaca_img,
               0 as is_owner, 1 as sort_order
        FROM anggota_booking ab
        JOIN users u ON ab.user_id = u.id_user
        WHERE ab.booking_id = ?
        ORDER BY sort_order ASC, nama ASC
    ";

        return $qb->fromRaw($unionSql, [$booking->user_id, $bookingId])
            ->paginate($perPage, $page);
    }

    public function findConflictingBookings(int $roomId, string $date, ?int $excludeId = null): array
    {
        $query = Booking::Query()->where('ruangan_id', $roomId)->where('tanggal_penggunaan_ruang', $date)->whereIn('status', ['verified', 'active']);

        if ($excludeId) {
            $query->where('id_booking', '!=', $excludeId);
        }

        return $query->get();
    }

    public function findUserBookingsOnDate(int $userId, string $date, ?int $excludeId = null): array
    {
        $query = Booking::Query()->where('user_id', $userId)->where('tanggal_penggunaan_ruang', $date)->whereIn('status', ['draft', 'pending', 'verified', 'active']);

        if ($excludeId) {
            $query->where('id_booking', '!=', $excludeId);
        }

        return $query->get();
    }

    public function findUserMemberBookingsOnDate(int $userId, string $date, ?int $excludeId = null): array
    {
        $qb = new QueryBuilder($this->database->pdo);

        $qb->table('booking b')->select(['b.id_booking', 'b.waktu_mulai', 'b.waktu_selesai', 'b.status'])
            ->join('anggota_booking ab', 'b.id_booking', '=', 'ab.booking_id')
            ->where('ab.user_id', $userId)
            ->where('b.tanggal_penggunaan_ruang', $date)
            ->whereIn('b.status', ['draft', 'pending', 'verified', 'active']);

        if ($excludeId) {
            $qb->where('b.id_booking', '!=', $excludeId);
        }

        return $qb->get();
    }

    public function countActiveBookings(int $userId): int
    {
        return Booking::Query()->where('user_id', $userId)->whereNotIn('status', ['completed', 'cancelled', 'expired', 'no_show'])->count();
    }

    public function isMemberOfBooking(int $bookingId, int $userId): bool
    {
        $qb = new QueryBuilder($this->database->pdo);

        $result = $qb->table('anggota_booking')
            ->where('booking_id', $bookingId)
            ->where('user_id', $userId)
            ->first();

        return $result !== null;
    }

    public function addMember(int $bookingId, int $userId): bool
    {
        $qb = new QueryBuilder($this->database->pdo);

        return $qb->table('anggota_booking')->insert([
            'booking_id' => $bookingId,
            'user_id' => $userId,
        ]);
    }

    public function getMemberCount(int $bookingId): int
    {
        $qb = new QueryBuilder($this->database->pdo);

        return $qb->table('anggota_booking')
            ->where('booking_id', $bookingId)
            ->count();
    }

    public function findUserById(int $userId): ?array
    {
        $qb = new QueryBuilder($this->database->pdo);

        return $qb->table('users')->where('id_user', $userId)->first();
    }

    public function removeMember(int $bookingId, int $userId): bool
    {
        $qb = new QueryBuilder($this->database->pdo);

        return $qb->table('anggota_booking')->where('booking_id', $bookingId)->where('user_id', $userId)->delete();
    }

    public function updateUserWarning(int $userId, int $warningLevel): void
    {
        $qb = new QueryBuilder($this->database->pdo);
        $qb->table('users')
            ->where('id_user', $userId)
            ->update(['peringatan' => $warningLevel]);
    }

    public function updateUserStatus(int $userId, string $status, ?string $suspendUntil = null): void
    {
        $data = ['status' => $status];

        if ($suspendUntil !== null) {
            $data['suspensi_terakhir'] = $suspendUntil;
        }
        $qb = new QueryBuilder($this->database->pdo);
        $qb->table('users')
            ->where('id_user', $userId)
            ->update($data);
    }

    public function isDateBlocked(string $date, ?int $ruanganId = null): bool
    {
        $qb = new QueryBuilder($this->database->pdo);

        $allBlocked = $qb->table('blocked_dates')
            ->where('tanggal_begin', '<=', $date)
            ->where('tanggal_end', '>=', $date)
            ->whereNull('ruangan_id')
            ->first();
        if ($allBlocked) {
            return true;
        }

        if ($ruanganId !== null) {
            $qb2 = new QueryBuilder($this->database->pdo);

            $roomBlocked = $qb2->table('blocked_dates')
                ->where('tanggal_begin', '<=', $date)
                ->where('tanggal_end', '>=', $date)
                ->where('ruangan_id', $ruanganId)
                ->first();
            return $roomBlocked !== null;
        }
        return false;
    }

    /**
     * Check if library is closed TODAY (all rooms blocked)
     */
    public function isLibraryClosedToday(): bool
    {
        $today = date('Y-m-d');

        $qb = new QueryBuilder($this->database->pdo);
        $result = $qb->table('blocked_dates')
            ->where('tanggal_begin', '<=', $today)
            ->where('tanggal_end', '>=', $today)
            ->whereNull('ruangan_id')
            ->whereNull('deleted_at')
            ->first();

        return $result !== null;
    }

    /**
     * Get closure reason for a specific date
     */
    public function getClosureReason(string $date): ?string
    {
        $qb = new QueryBuilder($this->database->pdo);

        $result = $qb->table('blocked_dates')
            ->select(['alasan'])
            ->where('tanggal_begin', '<=', $date)
            ->where('tanggal_end', '>=', $date)
            ->whereNull('ruangan_id')
            ->whereNull('deleted_at')
            ->first();

        return $result ? $result['alasan'] : null;
    }

    /**
     * Block date range for multiple rooms or all rooms
     * @param array $ruanganIds Array of room IDs to block (empty = all rooms)
     */
    public function blockDateRange(string $dateBegin, string $dateEnd, array $ruanganIds, string $reason, int $userId): void
    {
        $qb = new QueryBuilder($this->database->pdo);

        // If no specific rooms selected, block "all rooms" (ruangan_id = null)
        if (empty($ruanganIds)) {
            $qb->table('blocked_dates')->insert([
                'tanggal_begin' => $dateBegin,
                'tanggal_end' => $dateEnd,
                'ruangan_id' => null,
                'alasan' => $reason,
                'created_by' => $userId
            ]);
        } else {
            // Block each selected room
            foreach ($ruanganIds as $roomId) {
                $qb = new QueryBuilder($this->database->pdo); // Reset for each insert
                $qb->table('blocked_dates')->insert([
                    'tanggal_begin' => $dateBegin,
                    'tanggal_end' => $dateEnd,
                    'ruangan_id' => (int) $roomId,
                    'alasan' => $reason,
                    'created_by' => $userId
                ]);
            }
        }
    }

    /**
     * Find bookings affected by a date range block
     * Returns bookings that overlap with the blocked date range
     */
    public function findAffectedBookingsByDateRange(string $dateBegin, string $dateEnd, array $ruanganIds = []): array
    {
        $qb = new QueryBuilder($this->database->pdo);

        $query = $qb->table('booking b')
            ->select([
                'b.id_booking',
                'b.user_id',
                'b.ruangan_id',
                'b.tanggal_penggunaan_ruang',
                'b.waktu_mulai',
                'b.waktu_selesai',
                'b.status',
                'r.nama_ruangan',
                'u.nama as user_nama',
                'u.email as user_email'
            ])
            ->join('ruangan r', 'b.ruangan_id', '=', 'r.id_ruangan')
            ->join('users u', 'b.user_id', '=', 'u.id_user')
            ->where('b.tanggal_penggunaan_ruang', '>=', $dateBegin)
            ->where('b.tanggal_penggunaan_ruang', '<=', $dateEnd)
            ->whereIn('b.status', ['draft', 'pending', 'verified', 'active']);

        // If specific rooms are being blocked, filter by those rooms only
        // If empty array (all rooms), no additional filter needed
        if (!empty($ruanganIds)) {
            $query->whereIn('b.ruangan_id', $ruanganIds);
        }

        return $query->orderBy('b.tanggal_penggunaan_ruang', 'asc')
            ->orderBy('b.waktu_mulai', 'asc')
            ->get();
    }

    public function unblockDate(int $blockedDateId): void
    {
        $qb = new QueryBuilder($this->database->pdo);

        $qb->table('blocked_dates')->where('id_blocked_date', $blockedDateId)->delete();
    }

    public function getBlockedDates(): array
    {
        $qb = new QueryBuilder($this->database->pdo);

        return $qb->table('blocked_dates')
            ->select(['blocked_dates.*', 'ruangan.nama_ruangan'])
            ->leftJoin('ruangan', 'blocked_dates.ruangan_id', '=', 'ruangan.id_ruangan')
            ->orderBy('blocked_dates.tanggal_begin', 'asc')
            ->get();
    }
    private function baseBookingQuery(): QueryBuilder
    {
        $qb = new QueryBuilder($this->database->pdo);
        return $qb->table('booking b')
            ->setModel(Booking::class)
            ->select([
                'b.*',
                'r.nama_ruangan',
                'r.jenis_ruangan',
                'r.kapasitas_min as required_members',
                'r.kapasitas_max as maximum_members',
                'u.nama',
                'f.id_feedback',
                '(SELECT COUNT(*) FROM anggota_booking WHERE booking_id = b.id_booking) + 1 as current_members',
            ])
            ->join('ruangan r', 'b.ruangan_id', '=', 'r.id_ruangan')
            ->join('users u', 'b.user_id', '=', 'u.id_user')
            ->leftJoin('feedback f', 'b.id_booking', '=', 'f.booking_id');
    }

    public function delete(int $bookingId): bool
    {
        $qb = new QueryBuilder($this->database->pdo);
        return $qb->table('booking')
            ->where('id_booking', $bookingId)
            ->delete();
    }

    public function getAllRooms(): array
    {
        return Room::Query()
            ->whereIn('status_ruangan', ['available', 'adminOnly'])
            ->orderBy('nama_ruangan', 'asc')
            ->get();
    }
    public function getAllUsers(): array
    {
        return User::Query()->where('status', 'active')->get();
    }
    public function findUserByIdentifier(string $identifier): ?object
    {
        return User::Query()
            ->whereRaw(
                "(email = ? OR nim = ? OR nip = ? OR nama = ?)",
                [$identifier, $identifier, $identifier, $identifier]
            )
            ->first();
    }

    public function findRoomById(int $roomId): ?Room
    {
        return Room::Query()->where('id_ruangan', $roomId)->first();
    }
}