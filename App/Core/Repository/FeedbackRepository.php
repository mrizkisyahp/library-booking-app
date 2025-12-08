<?php

namespace App\Core\Repository;

use App\Core\QueryBuilder;
use App\Core\Database;
use App\Models\Feedback;
use App\Core\Paginator;

class FeedbackRepository
{
    public function __construct(private Database $database)
    {
    }

    public function findById(int $id): ?Feedback
    {
        return Feedback::Query()->where('id_feedback', $id)->first();
    }

    public function findByBookingId(int $bookingId): ?Feedback
    {
        return Feedback::Query()
            ->where('booking_id', $bookingId)
            ->first();
    }

    public function create(array $data): Feedback
    {
        $feedback = new Feedback();

        foreach ($data as $key => $value) {
            if (property_exists($feedback, $key)) {
                $feedback->{$key} = $value;
            }
        }

        $feedback->save();
        return $feedback;
    }

    public function getAllFeedbacks(array $filters = [], int $perPage = 15, int $page = 1): Paginator
    {
        $query = (new QueryBuilder($this->database->pdo))
            ->setModel(Feedback::class)
            ->table('feedback')
            ->select([
                'feedback.*',
                'users.nama',
                'users.email',
                'booking.tanggal_penggunaan_ruang',
                'booking.waktu_mulai',
                'booking.waktu_selesai',
                'booking.tujuan',
                'ruangan.nama_ruangan',
            ])
            ->leftJoin('users', 'feedback.user_id', '=', 'users.id_user')
            ->leftJoin('booking', 'feedback.booking_id', '=', 'booking.id_booking')
            ->leftJoin('ruangan', 'booking.ruangan_id', '=', 'ruangan.id_ruangan');

        if (!empty($filters['keyword'])) {
            $keyword = '%' . $filters['keyword'] . '%';
            $query->whereRaw("(users.nama LIKE ? OR ruangan.nama_ruangan LIKE ?)", [$keyword, $keyword]);
        }

        if (!empty($filters['rating'])) {
            $query->where('feedback.rating', $filters['rating']);
        }

        if (!empty($filters['tanggal'])) {
            $query->where('booking.tanggal_penggunaan_ruang', $filters['tanggal']);
        }

        $query->orderBy('feedback.created_at', 'DESC');

        return $query->paginate($perPage, $page);
    }

    public function findByIdWithDetails(int $id): ?object
    {
        $result = (new QueryBuilder($this->database->pdo))
            ->table('feedback')
            ->select([
                'feedback.*',
                'users.nama',
                'users.email',
                'users.nim',
                'users.nip',
                'booking.tanggal_booking',
                'booking.tanggal_penggunaan_ruang',
                'booking.waktu_mulai',
                'booking.waktu_selesai',
                'booking.tujuan',
                'ruangan.nama_ruangan',
                'ruangan.jenis_ruangan',
            ])
            ->leftJoin('users', 'feedback.user_id', '=', 'users.id_user')
            ->leftJoin('booking', 'feedback.booking_id', '=', 'booking.id_booking')
            ->leftJoin('ruangan', 'booking.ruangan_id', '=', 'ruangan.id_ruangan')
            ->where('feedback.id_feedback', $id)
            ->first();

        return $result ? (object) $result : null;
    }
}