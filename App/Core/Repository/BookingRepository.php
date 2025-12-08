<?php

namespace App\Core\Repository;

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

    public function getBookingMembers(int $bookingId): array
    {
        $qb = new QueryBuilder($this->database->pdo);

        // Get booking to find PIC
        $booking = $this->findById($bookingId);
        if (!$booking) {
            return [];
        }

        // Get members from anggota_booking
        $members = $qb->table('anggota_booking ab')
            ->select(['u.id_user', 'u.nama', 'u.email', 'u.nim', 'u.nip', 'u.kubaca_img', '0 as is_owner'])
            ->join('users u', 'ab.user_id', '=', 'u.id_user')
            ->where('ab.booking_id', $bookingId)
            ->get();

        // Add PIC at the beginning
        $qb2 = new QueryBuilder($this->database->pdo);
        $pic = $qb2->table('users')
            ->select(['id_user', 'nama', 'email', 'nim', 'nip', 'kubaca_img'])
            ->where('id_user', $booking->user_id)
            ->first();

        if ($pic) {
            array_unshift($members, [
                'id_user' => $pic['id_user'],
                'nama' => $pic['nama'],
                'email' => $pic['email'],
                'nim' => $pic['nim'],
                'nip' => $pic['nip'],
                'kubaca_img' => $pic['kubaca_img'],
                'is_owner' => 1,
            ]);
        }
        return $members;
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

    public function blockDateRange(string $dateBegin, string $dateEnd, ?int $ruanganId, string $reason, int $userId): void
    {
        $qb = new QueryBuilder($this->database->pdo);

        $qb->table('blocked_dates')->insert([
            'tanggal_begin' => $dateBegin,
            'tanggal_end' => $dateEnd,
            'ruangan_id' => $ruanganId,
            'alasan' => $reason,
            'created_by' => $userId
        ]);
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
}