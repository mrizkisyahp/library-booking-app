<?php

namespace App\Core\Services;

use App\Models\Room;
use App\Models\User;

class BookingValidator
{
    public const MAX_DURATION_HOURS = 3;
    public const MIN_DURATION_HOURS = 1;

    public static function validate(array $data, Room $room, ?User $user = null): array
    {
        $errors = [];

        $date = $data['tanggal_penggunaan_ruang'];
        $start = $data['waktu_mulai'] ?? '';
        $end = $data['waktu_selesai'] ?? '';
        $now = time();

        if (!$date || !$start || !$end) {
            return ['valid' => false, 'errors' => ['Tanggal dan waktu harus diisi.']];
        }

        $dateAt = strtotime("$date");
        $startAt = strtotime("$date $start");
        $endAt = strtotime("$date $end");
        $today = strtotime('today');

        if ($endAt <= $startAt) {
            $errors[] = 'Waktu selesai harus lebih besar dari waktu mulai';
        }

        $MinuteLater = strtotime('+15 minutes', strtotime(date('Y-m-d H:i')));
        if ($dateAt === $today && $startAt < $MinuteLater) {
            $errors[] = 'Waktu mulai harus minimal 15 menit setelah waktu sekarang.';
        }

        if ($dateAt < $today) {
            // error_log('Tanggal Hari ini : ' . $dateAt . 'Tanggal Sekarang : ' . $now);
            $errors[] = 'Booking hanya bisa dibuat maksimal 7 hari ke depan';
        }

        $duration = ($endAt - $startAt) / 3600;
        if ($duration < self::MIN_DURATION_HOURS) {
            // error_log('durasi : ' . $duration);
            $errors[] = 'Durasi minimal 1 jam';
        }

        $duration = ($endAt - $startAt) / 3600;
        if ($duration > self::MAX_DURATION_HOURS) {
            $errors[] = 'Durasi maksimal 3 jam.';
        }

        $openAt = strtotime("$date 08:00");
        $closeAt = strtotime("$date 16:20");
        if ($startAt < $openAt || $endAt > $closeAt) {
            $errors[] = 'Booking harus dalam jam operasional (08:00-16:20).';
        }

        $maxDate = strtotime('+7 days', strtotime(date('Y-m-d')));
        if ($startAt > strtotime(date('Y-m-d', $maxDate) . ' 23:59:59')) {
            $errors[] = 'Booking hanya bisa dibuat untuk 7 hari ke depan.';
        }
        
        $dayOfWeek = (int)date('N', strtotime($date));
        if ($dayOfWeek === 6 || $dayOfWeek === 7) {
            $errors[] = 'Booking tidak tersedia pada hari Sabtu dan Minggu.';
        }

        if (!$user || $user->status !== 'active') {
            $errors[] = 'Akun harus terverifikasi kubaca terlebih dahulu sebelum booking';
        }

        if (!$user || $user->status === 'rejected') {
            $errors[] = 'Akun tidak dapat melakukan peminjaman ruangan, silahkan upload kembali kubaca di profile';
        }

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
