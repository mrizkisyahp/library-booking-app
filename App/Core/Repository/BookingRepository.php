<?php

namespace App\Core\Repository;

use App\Models\Booking;
use App\Core\QueryBuilder;

class BookingRepository
{
    public function findById(int $id): ?Booking
    {
        return Booking::Query()->where('id_booking', $id)->first();
    }
    public function getUserActiveBookings(int $userId, int $limit = 10): array
    {
        return Booking::Query()
            ->select([
                'booking.*',
                'ruangan.nama_ruangan',
                'ruangan.jenis_ruangan',
                'ruangan.kapasitas_min AS required_members',
                'ruangan.kapasitas_max AS maximum_members',
                'feedback.id_feedback AS feedback_submitted',
                '(SELECT COUNT(*) FROM anggota_booking WHERE anggota_booking.booking_id = booking.id_booking) + 1 AS current_members'
            ])
            ->leftJoin('ruangan', 'booking.ruangan_id', '=', 'ruangan.id_ruangan')
            ->leftJoin('feedback', 'booking.id_booking', '=', 'feedback.booking_id')
            ->where('booking.user_id', $userId)
            ->whereNotIn('booking.status', ['completed', 'cancelled', 'expired'])
            ->orderBy('booking.tanggal_penggunaan_ruang', 'DESC')
            ->orderBy('booking.waktu_mulai', 'DESC')
            ->limit($limit)
            ->get();
    }

    public function getUserPendingFeedbacks(int $userId): array
    {
        return Booking::Query()
            ->select([
                'booking.id_booking',
                'booking.tanggal_penggunaan_ruang',
                'booking.waktu_mulai',
                'ruangan.nama_ruangan'
            ])
            ->leftJoin('ruangan', 'booking.ruangan_id', '=', 'ruangan.id_ruangan')
            ->leftJoin('feedback', 'booking.id_booking', '=', 'feedback.booking_id')
            ->where('booking.user_id', $userId)
            ->where('booking.status', 'completed')
            ->whereNull('feedback.id_feedback')
            ->orderBy('booking.tanggal_penggunaan_ruang', 'DESC')
            ->get();
    }

    public function getTotalBookings(): int
    {
        return Booking::Query()->count();
    }

    public function getBookingCountByStatus(): array
    {
        $results = Booking::Query()->raw('SELECT status, COUNT(*) as count
        FROM booking
        GROUP BY status');

        $statuses = [
            'draft' => 0,
            'pending' => 0,
            'verified' => 0,
            'active' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'expired' => 0,
            'no_show' => 0,
        ];
        foreach ($results as $row) {
            $statuses[$row['status']] = (int) $row['count'];
        }
        return $statuses;
    }

    public function getRecentBookings(int $limit = 10): array
    {
        return Booking::Query()
            ->select([
                'booking.*',
                'users.nama as nama',
                'ruangan.nama_ruangan as nama_ruangan',
                'ruangan.jenis_ruangan',
                'ruangan.kapasitas_min AS required_members',
                'ruangan.kapasitas_max AS maximum_members',
                'feedback.id_feedback as id_feedback',
                'booking.waktu_selesai',
                '(SELECT COUNT(*) FROM anggota_booking WHERE anggota_booking.booking_id = booking.id_booking) + 1 AS current_members'
            ])

            ->leftJoin('users', 'booking.user_id', '=', 'users.id_user')
            ->leftJoin('ruangan', 'booking.ruangan_id', '=', 'ruangan.id_ruangan')
            ->leftJoin('feedback', 'booking.id_booking', '=', 'feedback.booking_id')
            ->orderBy('booking.created_at', 'DESC')
            ->limit($limit)
            ->get();
    }

    public function getBookingsByStatus(string $status): array
    {
        return Booking::Query()
            ->select([
                'booking.*',
                'users.nama as nama',
                'ruangan.nama_ruangan as nama_ruangan',
                'ruangan.jenis_ruangan',
                'ruangan.kapasitas_min AS required_members',
                'ruangan.kapasitas_max AS maximum_members',
                'feedback.id_feedback as id_feedback',
                'booking.waktu_selesai',
                '(SELECT COUNT(*) FROM anggota_booking WHERE anggota_booking.booking_id = booking.id_booking) + 1 AS current_members'
            ])
            ->leftJoin('users', 'booking.user_id', '=', 'users.id_user')
            ->leftJoin('ruangan', 'booking.ruangan_id', '=', 'ruangan.id_ruangan')
            ->leftJoin('feedback', 'booking.id_booking', '=', 'feedback.booking_id')
            ->where('booking.status', $status)
            ->orderBy('booking.created_at', 'DESC')
            ->get();
    }

    public function getRoomUsageStatistics(): array
    {
        return Booking::Query()->raw("
        SELECT 
            ruangan.nama_ruangan,
            COUNT(booking.id_booking) as booking_count
        FROM booking
        LEFT JOIN ruangan ON booking.ruangan_id = ruangan.id_ruangan
        GROUP BY ruangan.id_ruangan, ruangan.nama_ruangan
        ORDER BY booking_count DESC
    ");
    }

    public function findConflictingBooking(int $roomId, string $date, string $startTime, string $endTime, ?int $excludeBookingId = null): array
    {
        $query = Booking::Query()
            ->where('ruangan_id', $roomId)
            ->where('tanggal_penggunaan_ruang', $date)
            ->whereIn('status', ['verified', 'active']);

        if ($excludeBookingId) {
            $query->where('id_booking', '!=', $excludeBookingId);
        }
        return $query->get();
    }

    public function countActiveBookings(int $userId): int
    {
        return Booking::query()
            ->where('user_id', $userId)
            ->whereNotIn('status', ['completed', 'cancelled', 'expired', 'no_show'])
            ->count();
    }

    public function findRoomById(int $roomId): ?array
    {
        $qb = new QueryBuilder(\App\Core\App::$app->db->pdo);
        return $qb->table('ruangan')->where('id_ruangan', $roomId)->first();
    }

    public function findUserBookingsOnDate(int $userId, string $date, ?int $excludeId = null): array
    {
        $query = Booking::query()
            ->where('user_id', $userId)
            ->where('tanggal_penggunaan_ruang', $date)
            ->whereNotIn('status', ['cancelled', 'expired', 'no_show']);
        if ($excludeId) {
            $query->where('id_booking', '!=', $excludeId);
        }
        return $query->get();
    }

    public function findUserMemberBookingsOnDate(int $userId, string $date, ?int $excludeId = null): array
    {
        $qb = new QueryBuilder(\App\Core\App::$app->db->pdo);

        $qb->table('booking b')
            ->select([
                'b.id_booking',
                'b.waktu_mulai',
                'b.waktu_selesai',
                'b.status'
            ])
            ->join('anggota_booking ab', 'b.id_booking', '=', 'ab.booking_id')
            ->where('ab.user_id', $userId)
            ->where('b.tanggal_penggunaan_ruang', $date)
            ->whereNotIn('b.status', ['cancelled', 'expired', 'no_show']);

        if ($excludeId) {
            $qb->where('b.id_booking', '!=', $excludeId);
        }

        return $qb->get();
    }
}