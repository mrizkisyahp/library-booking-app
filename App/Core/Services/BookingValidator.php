<?php

namespace App\Core\Services;

use App\Models\Room;
use App\Models\User;

class BookingValidator
{
    public const MAX_DURATION_HOURS = 3;

    public static function validate(array $data, Room $room, ?User $user = null): array
    {
        $errors = [];

        $date = $data['tanggal_penggunaan_ruang'];
        $start = $data['waktu_mulai'] ?? '';
        $end = $data['waktu_selesai'] ?? '';

        if (!$date || !$start || !$end) {
            return ['valid' => false, 'errors' => ['Tanggal dan waktu harus diisi.']];
        }

        $startAt = strtotime("$date $start");
        $endAt = strtotime("$date $end");

        if ($endAt <= $startAt) {
            $errors[] = 'Waktu selesai harus lebih besar dari waktu mulai.';
        }

        $duration = ($endAt - $startAt) / 3600;
        if ($duration > self::MAX_DURATION_HOURS) {
            $errors[] = 'Durasi maksimal 3 jam.';
        }

        // $openAt = strtotime("$date 08:00");
        // $closeAt = strtotime("$date 21:00");
        // if ($startAt < $openAt || $endAt > $closeAt) {
        //     $errors[] = 'Booking harus dalam jam operasional (08:00-21:00).';
        // }

        $maxDate = strtotime('+7 days', strtotime(date('Y-m-d')));
        if ($startAt > strtotime(date('Y-m-d', $maxDate) . ' 23:59:59')) {
            $errors[] = 'Booking hanya bisa dibuat untuk 7 hari ke depan.';
        }

        // $dayOfWeek = (int)date('N', strtotime($date));
        // if ($dayOfWeek === 6 || $dayOfWeek === 7) {
        //     $errors[] = 'Booking tidak tersedia pada hari Sabtu dan Minggu.';
        // }

        $requiresPegawaiFields = $user && $user->isDosen() && strcasecmp($room->nama_ruangan, 'Ruang Rapat') === 0;

        if ($requiresPegawaiFields) {
            if (empty($data['pegawai_reason'])) {
                $errors[] = 'Pegawai harus memilih alasan peminjaman.';
            }
            if (empty($_FILES['pegawai_file']['name'] ?? '')) {
                $errors[] = 'Pegawai wajib mengunggah berkas pendukung.';
            }
        }

        return ['valid' => empty($errors), 'errors' => $errors];
    }
}
