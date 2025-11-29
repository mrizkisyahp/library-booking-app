<?php

namespace App\Core\Services;

use App\Core\App;
use App\Models\Booking;
use App\Models\Feedback;
use App\Models\User;
use App\Core\Services\Logger;

class FeedbackService
{
    public function getFeedbackForm(int $bookingId, int $userId): array
    {
        $booking = Booking::Query()->where('id_booking', $bookingId)->first();

        if (!$booking || $booking->user_id !== $userId || $booking->status !== 'completed') {
            return [
                'success' => false,
                'message' => 'Booking tidak valid.',
                'redirect' => '/dashboard',
            ];
        }

        if (Feedback::Query()->where('booking_id', $bookingId)->first()) {
            return [
                'success' => false,
                'message' => 'Feedback untuk booking ini sudah dikirim.',
                'redirect' => '/dashboard',
            ];
        }

        return [
            'success' => true,
            'data' => [
                'booking' => $booking,
            ],
        ];
    }

    public function submitFeedback(int $bookingId, int $userId, array $input): array
    {
        $booking = Booking::Query()->where('id_booking', $bookingId)->first();
        if (!$booking || $booking->user_id !== $userId || $booking->status !== 'completed') {
            return [
                'success' => false,
                'message' => 'Booking tidak valid.',
                'redirect' => '/dashboard',
            ];
        }

        if (Feedback::Query()->where('booking_id', $bookingId)->first()) {
            return [
                'success' => false,
                'message' => 'Feedback untuk booking ini sudah dikirim.',
                'redirect' => '/dashboard',
            ];
        }

        $serviceRating = (int) ($input['service_rating'] ?? 0);
        $roomRating = (int) ($input['room_rating'] ?? 0);

        if ($serviceRating < 1 || $serviceRating > 5 || $roomRating < 1 || $roomRating > 5) {
            return [
                'success' => false,
                'message' => 'Rating harus di antara 1 sampai 5.',
                'redirect' => '/feedback/create?booking=' . $bookingId,
            ];
        }

        $feedback = new Feedback();
        $feedback->booking_id = $bookingId;
        $feedback->user_id = $userId;
        $feedback->rating = round(($serviceRating + $roomRating) / 2, 1);
        $feedback->komentar = $input['comments'] ?? null;

        if ($feedback->save()) {
            Logger::info('Feedback submitted', [
                'user_id' => $userId,
                'booking_id' => $bookingId,
                'rating' => $feedback->rating,
            ]);
            return [
                'success' => true,
                'message' => 'Terima kasih atas feedback Anda.',
                'redirect' => '/dashboard',
            ];
        }

        Logger::error('Failed to save feedback', [
            'user_id' => $userId,
            'booking_id' => $bookingId,
        ]);

        return [
            'success' => false,
            'message' => 'Gagal menyimpan feedback.',
            'redirect' => '/dashboard',
        ];
    }

    public function userHasPendingFeedback(int $userId): bool
    {
        $stmt = App::$app->db->prepare("
            SELECT COUNT(*) AS cnt
            FROM booking b
            WHERE b.user_id = :user_id
              AND b.status = 'completed'
              AND NOT EXISTS (
                SELECT 1 FROM feedback f WHERE f.booking_id = b.id_booking
              )
        ");
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn() > 0;
    }

    public function getPendingFeedbackBookings(int $userId): array
    {
        $stmt = App::$app->db->prepare("
            SELECT b.id_booking, b.tanggal_penggunaan_ruang, b.waktu_mulai, r.nama_ruangan
            FROM booking b
            JOIN ruangan r ON r.id_ruangan = b.ruangan_id
            WHERE b.user_id = :user_id
              AND b.status = 'completed'
              AND NOT EXISTS (
                SELECT 1 FROM feedback f WHERE f.booking_id = b.id_booking
              )
            ORDER BY b.tanggal_penggunaan_ruang DESC, b.waktu_mulai DESC
        ");
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getFeedbackForBooking(int $bookingId): ?Feedback
    {
        return Feedback::Query()->where('booking_id', $bookingId)->first();
    }

    public function getAdminFeedbackList(): array
    {
        $sql = "
            SELECT f.*, u.nama AS user_name, b.tanggal_penggunaan_ruang, b.waktu_mulai, r.nama_ruangan
            FROM feedback f
            JOIN users u ON u.id_user = f.user_id
            JOIN booking b ON b.id_booking = f.booking_id
            JOIN ruangan r ON r.id_ruangan = b.ruangan_id
            ORDER BY f.created_at DESC
        ";

        $stmt = App::$app->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
