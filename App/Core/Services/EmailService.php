<?php

namespace App\Core\Services;

use App\Core\App;
use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

class EmailService
{   

    private static function configureMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $_ENV['MAIL_HOST'];
        $mail->SMTPAuth = false;
        $mail->Username = $_ENV['MAIL_USERNAME'];
        $mail->Password = $_ENV['MAIL_PASSWORD'];
        $mail->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
        $mail->Port = (int)($_ENV['MAIL_PORT']);
        $mail->isHTML(true);
        return $mail;
    }

    public static function send(string $to, string $toName, string $subject, string $body, bool $ccSelf = false): bool
    {
        try {
            $mail = self::configureMailer();
            $fromEmail = $_ENV['MAIL_FROM_ADDRESS'];
            $fromName = $_ENV['MAIL_FROM_NAME'];
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to, $toName);
            if ($ccSelf) $mail->addCC($to);

            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();
            return true;
        } catch (MailException $e) {
            error_log('Email error: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendVerificationCode(User $user, string $otp, string $purpose = 'register'): bool
    {
        $subject = $purpose === 'reset_password'
            ? 'Password Reset Request | Library Booking App'
            : 'Account Verification Code | Library Booking App';

        $intro = $purpose === 'reset_password'
            ? 'Kami menerima permintaan untuk mereset kata sandi akun kamu.'
            : 'Selamat datang! Berikut adalah kode verifikasi akun kamu.';

        $note = $purpose === 'reset_password'
            ? 'Jika kamu tidak meminta pengaturan ulang kata sandi, abaikan email ini.'
            : 'Jangan bagikan kode ini kepada siapa pun demi keamanan akunmu.';

        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p>{$intro}</p>
            <p>Kode verifikasi: <strong>{$otp}</strong></p>
            <p>Kode ini berlaku selama 15 menit.</p>
            <p>{$note}</p>
            <p>Library Booking App PNJ</p>
        ";

        return self::send($user->email, $user->nama, $subject, $body);
    }

    public static function sendKubacaVerified(User $user): bool
    {
        $subject = 'KuBaca Verified | Library Booking App';
        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p>Selamat! KuBaca kamu telah diverifikasi oleh admin.</p>
            <p>Akun kamu sekarang sudah fully verified dan bisa menggunakan semua fitur Library Booking App.</p>
            <p>Terima kasih,<br>Library Booking App PNJ</p>
        ";

        return self::send($user->email, $user->nama, $subject, $body);
    }

    // send valid notif
    public static function sendBookingValidated(User $user, $booking): bool
    {
        $subject = 'Booking Validated | Library Booking App';
        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p>Reservasi ruangan kamu telah divalidasi oleh admin!</p>
            <p><strong>Booking Code:</strong> {$booking->booking_code}</p>
            <p><strong>Tanggal:</strong> {$booking->booking_date}</p>
            <p><strong>Waktu:</strong> {$booking->start_time} - {$booking->end_time}</p>
            <p>Jangan lupa untuk check-in 10 menit sebelum waktu reservasi dimulai.</p>
            <p>Terima kasih,<br>Library Booking App PNJ</p>
        ";

        return self::send($user->email, $user->nama, $subject, $body);
    }

    // send cancel notif
    public static function sendBookingCancelled(User $user, $booking, string $reason = ''): bool
    {

        $reasonText = $reason ? "<p><strong>Alasan:</strong> {$reason}</p>" : '';

        $subject = 'Booking Cancelled | Library Booking App';
        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p>Reservasi ruangan kamu telah dibatalkan.</p>
            <p><strong>Booking Code:</strong> {$booking->booking_code}</p>
            <p><strong>Tanggal:</strong> {$booking->booking_date}</p>
            <p><strong>Waktu:</strong> {$booking->start_time} - {$booking->end_time}</p>
            {$reasonText}
            <p>Kamu bisa membuat reservasi baru jika diperlukan.</p>
            <p>Terima kasih,<br>Library Booking App PNJ</p>
        ";

        return self::send($user->email, $user->nama, $subject, $body);
    }

    // send feedback notif
    public static function sendFeedbackRequest(User $user, $booking): bool
    {
        $subject = 'Berikan Feedback Anda | Library Booking App';
        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p>Terima kasih telah menggunakan ruangan kami!</p>
            <p><strong>Booking Code:</strong> {$booking->booking_code}</p>
            <p><strong>Tanggal:</strong> {$booking->booking_date}</p>
            <p>Kami ingin mendengar pengalaman kamu. Silakan berikan feedback melalui sistem kami.</p>
            <p><a href=\"" . ($_ENV['APP_URL'] ?? 'http://localhost') . "/feedback?booking_id={$booking->id}\">Berikan Feedback</a></p>
            <p>Terima kasih,<br>Library Booking App PNJ</p>
        ";

        return self::send($user->email, $user->nama, $subject, $body);
    }

    // send check in remind
    public static function sendCheckInReminder(User $user, $booking): bool
    {

        $subject = 'Reminder: Check-in Reservasi Anda | Library Booking App';
        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p>Reservasi ruangan kamu akan dimulai dalam 10 menit!</p>
            <p><strong>Booking Code:</strong> {$booking->booking_code}</p>
            <p><strong>Tanggal:</strong> {$booking->booking_date}</p>
            <p><strong>Waktu:</strong> {$booking->start_time} - {$booking->end_time}</p>
            <p><strong>PENTING:</strong> Jangan lupa untuk check-in sebelum waktu dimulai, atau reservasi akan otomatis dibatalkan.</p>
            <p>Terima kasih,<br>Library Booking App PNJ</p>
        ";

        return self::send($user->email, $user->nama, $subject, $body);
    }

}
