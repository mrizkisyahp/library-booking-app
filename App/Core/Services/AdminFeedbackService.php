<?php

namespace App\Core\Services;

use App\Core\App;
use App\Models\Booking;
use App\Models\Feedback;
use App\Models\Room;
use App\Models\User;

class AdminFeedbackService
{
    public function listFeedback(array $filters = []): array
    {
        $db = App::$app->db;

        $sql = "
            SELECT
                f.*,
                u.nama AS user_name,
                b.id_booking,
                b.tanggal_penggunaan_ruang,
                b.waktu_mulai,
                r.nama_ruangan
            FROM feedback f
            JOIN booking b ON b.id_booking = f.booking_id
            LEFT JOIN users u ON u.id_user = f.user_id
            LEFT JOIN ruangan r ON r.id_ruangan = b.ruangan_id
            WHERE 1=1
        ";

        $params = [];
        $cleanFilters = [
            'booking_id' => null,
            'user_id' => null,
            'room_id' => null,
            'rating_min' => null,
            'rating_max' => null,
            'date_start' => null,
            'date_end' => null,
        ];

        if (!empty($filters['booking_id'])) {
            $sql .= " AND f.booking_id = :booking_id";
            $params[':booking_id'] = (int)$filters['booking_id'];
            $cleanFilters['booking_id'] = (int)$filters['booking_id'];
        }

        if (!empty($filters['user_id'])) {
            $sql .= " AND f.user_id = :user_id";
            $params[':user_id'] = (int)$filters['user_id'];
            $cleanFilters['user_id'] = (int)$filters['user_id'];
        }

        if (!empty($filters['room_id'])) {
            $sql .= " AND b.ruangan_id = :room_id";
            $params[':room_id'] = (int)$filters['room_id'];
            $cleanFilters['room_id'] = (int)$filters['room_id'];
        }

        if (!empty($filters['rating_min'])) {
            $sql .= " AND f.rating >= :rating_min";
            $params[':rating_min'] = (int)$filters['rating_min'];
            $cleanFilters['rating_min'] = (int)$filters['rating_min'];
        }

        if (!empty($filters['rating_max'])) {
            $sql .= " AND f.rating <= :rating_max";
            $params[':rating_max'] = (int)$filters['rating_max'];
            $cleanFilters['rating_max'] = (int)$filters['rating_max'];
        }

        if (!empty($filters['date_start'])) {
            $sql .= " AND DATE(f.created_at) >= :date_start";
            $params[':date_start'] = $filters['date_start'];
            $cleanFilters['date_start'] = $filters['date_start'];
        }

        if (!empty($filters['date_end'])) {
            $sql .= " AND DATE(f.created_at) <= :date_end";
            $params[':date_end'] = $filters['date_end'];
            $cleanFilters['date_end'] = $filters['date_end'];
        }

        $sql .= " ORDER BY f.created_at DESC";

        $stmt = $db->prepare($sql);
        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $paramType);
        }
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'feedback' => $rows,
            'filters' => $cleanFilters,
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
