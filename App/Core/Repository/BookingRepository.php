<?php

namespace App\Core\Repository;

use App\Models\Booking;

class BookingRepository
{
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
        return Booking::Query()
            ->select([
                'ruangan.nama_ruangan',
                'COUNT(booking.id_booking) as booking_count'
            ])
            ->leftJoin('ruangan', 'booking.ruangan_id', '=', 'ruangan.id_ruangan')
            ->groupBy('ruangan.id_ruangan', 'ruangan.nama_ruangan')
            ->orderBy('booking_count', 'DESC')
            ->get();
    }
}