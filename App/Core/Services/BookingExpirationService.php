<?php

namespace App\Core\Services;

use App\Core\App;
use App\Core\Services\Logger;
use App\Models\Booking;
use App\Models\User;
use DateInterval;
use DateTimeImmutable;

class BookingExpirationService
{
    private const CHECKIN_THRESHOLD_MINUTES = 5;

    public function expireDraftBookings(): array
    {
        $db = App::$app->db;
        $stmt = $db->prepare("SELECT * FROM booking WHERE status = 'draft'");
        $stmt->execute();
        $drafts = $stmt->fetchAll(\PDO::FETCH_CLASS, Booking::class);

        $bookingService = new UserBookingService();
        $now = new DateTimeImmutable();
        $expired = [];

        foreach ($drafts as $booking) {
            $shouldExpire = false;
            $reason = 'Waktu kadaluarsa';
            $bookingDate = $booking->tanggal_penggunaan_ruang ? new DateTimeImmutable($booking->tanggal_penggunaan_ruang) : null;
            $startDateTime = ($booking->tanggal_penggunaan_ruang && $booking->waktu_mulai)
                ? new DateTimeImmutable($booking->tanggal_penggunaan_ruang . ' ' . $booking->waktu_mulai)
                : null;
            $createdAt = $booking->created_at ? new DateTimeImmutable($booking->created_at) : null;

            if ($bookingDate && $bookingDate < $now->setTime(0, 0)) {
                $shouldExpire = true;
                $reason = 'Tanggal booking telah lewat';
            }

            if (!$shouldExpire && $startDateTime) {
                $minutesToStart = ($startDateTime->getTimestamp() - $now->getTimestamp()) / 60;
                if ($minutesToStart <= self::CHECKIN_THRESHOLD_MINUTES) {
                    $shouldExpire = true;
                    $reason = 'Kurang dari lima menit sebelum waktu mulai';
                }
            }

            if (!$shouldExpire && $createdAt) {
                if ($now->getTimestamp() - $createdAt->getTimestamp() >= 86400) {
                    $shouldExpire = true;
                    $reason = 'Melebihi 24 jam sejak dibuat';
                }
            }

            if (!$shouldExpire && !$bookingService->meetsMemberMinimum($booking)) {
                $shouldExpire = true;
                $reason = 'Belum memenuhi jumlah anggota minimum';
            }

            if (!$shouldExpire) {
                continue;
            }

            $booking->status = 'expired';
            $booking->save();

            $pic = User::Query()->where('id_user', $booking->user_id)->first();
            if ($pic instanceof User) {
                $pic->peringatan = (int) $pic->peringatan + 1;
                $pic->save();
            }

            Logger::booking('expired', (int) $booking->user_id, (int) $booking->id_booking, [
                'reason' => $reason,
            ]);

            $expired[] = [
                'booking_id' => $booking->id_booking,
                'reason' => $reason,
            ];
        }

        return [
            'success' => true,
            'expired_count' => count($expired),
            'expired_bookings' => $expired,
        ];
    }
}
