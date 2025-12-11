<?php

namespace App\Services;

use App\Repositories\FeedbackRepository;
use App\Repositories\BookingRepository;
use Exception;

class FeedbackService
{
    public function __construct(
        private FeedbackRepository $feedbackRepo,
        private BookingRepository $bookingRepo
    ) {
    }

    public function createFeedback(int $bookingId, int $userId, array $data): void
    {
        $booking = $this->bookingRepo->findById($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        if ((int) $booking->user_id !== $userId) {
            throw new Exception('Hanya PIC yang dapat memberikan feedback');
        }

        if ($booking->status !== 'completed') {
            throw new Exception('Feedback hanya dapat diberikan untuk booking yang sudah selesai');
        }

        $existingFeedback = $this->feedbackRepo->findByBookingId($bookingId);
        if ($existingFeedback) {
            throw new Exception('Feedback sudah pernah diberikan untuk booking ini');
        }

        $rating = (float) $data['rating'];
        if ($rating < 1.0 || $rating > 5.0) {
            throw new Exception('Rating harus antara 1.0 sampai 5.0');
        }

        $this->feedbackRepo->create([
            'booking_id' => $bookingId,
            'user_id' => $userId,
            'rating' => $rating,
            'komentar' => trim($data['komentar'] ?? ''),
        ]);
    }

    public function getBookingForFeedback(int $bookingId, int $userId): array
    {
        $booking = $this->bookingRepo->findByIdWithDetails($bookingId);

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan');
        }

        if ((int) $booking->user_id !== $userId) {
            throw new Exception('Anda tidak memiliki akses ke booking ini');
        }

        if ($booking->status !== 'completed') {
            throw new Exception('Feedback hanya dapat diberikan untuk booking yang sudah selesai');
        }

        $existingFeedback = $this->feedbackRepo->findByBookingId($bookingId);
        if ($existingFeedback) {
            throw new Exception('Feedback sudah pernah diberikan untuk booking ini');
        }

        return ['booking' => $booking];
    }

}