<?php

namespace App\Core\Services;

use App\Core\App;
use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

class EmailService
{
    public function __construct(
        private string $host,
        private string $username,
        private string $password,
        private string $encryption,
        private int $port,
        private string $fromAddress,
        private string $fromName,
    ) {
    }

    private function configureMailer(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $this->host;
        $mail->SMTPAuth = false;
        $mail->Username = $this->username;
        $mail->Password = $this->password;
        $mail->SMTPSecure = $this->encryption;
        $mail->Port = $this->port;
        $mail->isHTML(true);
        return $mail;
    }

    public function send(string $to, string $toName, string $subject, string $body, bool $ccSelf = false): bool
    {
        try {
            $mail = $this->configureMailer();
            $mail->setFrom($this->fromAddress, $this->fromName);
            $mail->addAddress($to, $toName);
            if ($ccSelf)
                $mail->addCC($to);

            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();
            return true;
        } catch (MailException $e) {
            error_log('Email error: ' . $e->getMessage());
            return false;
        }
    }

    public function sendVerificationCode(User $user, string $otp, string $purpose = 'register'): bool
    {
        $subject = $purpose === 'reset'
            ? 'Password Reset Request | Library Booking App'
            : 'Account Verification Code | Library Booking App';

        $intro = $purpose === 'reset'
            ? 'Kami menerima permintaan untuk reset kata sandi akun kamu.'
            : 'Selamat datang! Berikut adalah kode verifikasi akun kamu.';

        $note = $purpose === 'reset'
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

        return $this->send($user->email, $user->nama, $subject, $body);
    }

    public function sendKubacaVerified(User $user): bool
    {
        $subject = 'KuBaca Verified | Library Booking App';
        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p>Selamat! KuBaca kamu telah diverifikasi oleh admin.</p>
            <p>Akun kamu sekarang sudah fully verified dan bisa menggunakan semua fitur Library Booking App.</p>
            <p>Terima kasih,<br>Library Booking App PNJ</p>
        ";

        return $this->send($user->email, $user->nama, $subject, $body);
    }

    // send valid notif
    public function sendBookingValidated(User $user, $booking): bool
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

        return $this->send($user->email, $user->nama, $subject, $body);
    }

    // send cancel notif
    public function sendBookingCancelled(User $user, $booking, string $reason = ''): bool
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

        return $this->send($user->email, $user->nama, $subject, $body);
    }

    // send feedback notif
    public function sendFeedbackRequest(User $user, $booking): bool
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

        return $this->send($user->email, $user->nama, $subject, $body);
    }

    // send check in remind
    public function sendCheckInReminder(User $user, $booking): bool
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

        return $this->send($user->email, $user->nama, $subject, $body);
    }

    public function sendPasswordResetLink(User $user, string $resetLink): bool
    {
        $subject = 'Password Reset Request | Library Booking App';

        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p>Kami menerima permintaan untuk reset kata sandi akun kamu.</p>
            <p>Klik link berikut untuk mengatur ulang kata sandi:</p>
            <p><a href=\"{$resetLink}\" style=\"display: inline-block; padding: 10px 20px; background-color: #10b981; color: white; text-decoration: none; border-radius: 5px;\">Reset Password</a></p>
            <p>Atau salin link ini ke browser kamu:</p>
            <p><a href=\"{$resetLink}\">{$resetLink}</a></p>
            <p><strong>Link ini akan kadaluarsa dalam 15 menit.</strong></p>
            <p>Jika kamu tidak meminta reset password, abaikan email ini.</p>
            <p>Terima kasih,<br>Library Booking App PNJ</p>
        ";

        return $this->send($user->email, $user->nama, $subject, $body);
    }

    public function sendPasswordChangedNotification(User $user): bool
    {
        $subject = 'Password Changed | Library Booking App';

        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p>Password untuk akun kamu telah berhasil diubah.</p>
            <p><strong>Email:</strong> {$user->email}</p>
            <p><strong>Waktu:</strong> " . date('d F Y, H:i') . " WIB</p>
            <p><strong>Jika kamu tidak melakukan perubahan ini, segera hubungi admin atau reset password kamu.</strong></p>
            <p>Untuk keamanan akun kamu, pastikan:</p>
            <ul>
                <li>Gunakan password yang kuat dan unik</li>
                <li>Jangan bagikan password kepada siapa pun</li>
                <li>Logout dari perangkat yang tidak digunakan</li>
            </ul>
            <p>Terima kasih,<br>Library Booking App PNJ</p>
        ";

        return $this->send($user->email, $user->nama, $subject, $body);
    }

}
