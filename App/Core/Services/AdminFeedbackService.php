<?php

namespace App\Core\Services;

use App\Core\App;
use App\Models\Booking;
use App\Models\Feedback;
use App\Models\Room;
use App\Models\User;

class AdminFeedbackService
{
    private const PER_PAGE = 20;
    public function listFeedback(array $filters = []): array
    {
        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = (int) ($filters['perPage'] ?? self::PER_PAGE);

        $queryFilters = [
            'keyword' => $filters['keyword'] ?? null,
            'status' => $filters['status'] ?? null,
            'tanggal_penggunaan_ruang' => $filters['tanggal_penggunaan_ruang'] ?? null,
            'rating' => $filters['rating'] ?? null,
        ];

        $feedback = Feedback::findPaginated($page, $perPage, $queryFilters);

        return [
            'success' => true,
            'data' => [
                'feedback' => $feedback,
                'filters' => $queryFilters,
                'currentPage' => $page,
                'perPage' => $perPage,
                'total' => Feedback::count($queryFilters),
            ],
        ];
    }

    public function getFeedbackDetail(int $feedbackId): array
    {
        $db = App::$app->db;

        $stmt = $db->prepare("
            SELECT
                f.*,
                u.nama AS user_name,
                b.*,
                r.nama_ruangan,
                r.kapasitas_min,
                r.kapasitas_max
            FROM feedback f
            JOIN booking b ON b.id_booking = f.booking_id
            LEFT JOIN users u ON u.id_user = f.user_id
            LEFT JOIN ruangan r ON r.id_ruangan = b.ruangan_id
            WHERE f.id_feedback = :id
            LIMIT 1
        ");
        $stmt->bindValue(':id', $feedbackId, \PDO::PARAM_INT);
        $stmt->execute();

        $detail = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$detail) {
            return [
                'success' => false,
                'message' => 'Feedback not found.',
            ];
        }

        $booking = Booking::findOne(['id_booking' => $detail['booking_id']]);
        $room = $booking ? Room::findOne(['id_ruangan' => $booking->ruangan_id]) : null;
        $pic = $booking ? User::findOne(['id_user' => $booking->user_id]) : null;
        $feedbackUser = isset($detail['user_id']) ? User::findOne(['id_user' => $detail['user_id']]) : null;
        $members = $booking ? $booking->getMembers() : [];

        return [
            'success' => true,
            'data' => [
                'feedback' => $detail,
                'booking' => $booking,
                'room' => $room,
                'pic' => $pic,
                'feedbackUser' => $feedbackUser,
                'members' => $members,
            ],
        ];
    }
}
