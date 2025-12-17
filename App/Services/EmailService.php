<?php

namespace App\Services;

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
            <p><strong>Ruangan:</strong> {$booking->nama_ruangan}</p>
            <p><strong>Tanggal:</strong> {$booking->tanggal_penggunaan_ruang}</p>
            <p><strong>Waktu:</strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
            <p><strong>Check-in code:</strong> {$booking->checkin_code}</p>
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
           <p><strong>Ruangan:</strong> {$booking->nama_ruangan}</p>
            <p><strong>Tanggal:</strong> {$booking->tanggal_penggunaan_ruang}</p>
            <p><strong>Waktu:</strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
            {$reasonText}
            <p>Kamu bisa membuat reservasi baru jika diperlukan.</p>
            <p>Terima kasih,<br>Library Booking App PNJ</p>
        ";

        return $this->send($user->email, $user->nama, $subject, $body);
    }

    // send submitted notif
    public function sendBookingSubmitted(User $user, $booking): bool
    {
        $subject = 'Booking Submitted | Library Booking App';
        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p>Reservasi ruangan kamu telah berhasil disubmit dan sedang menunggu persetujuan admin.</p>
            <p><strong>Ruangan:</strong> {$booking->nama_ruangan}</p>
            <p><strong>Tanggal:</strong> {$booking->tanggal_penggunaan_ruang}</p>
            <p><strong>Waktu:</strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
            <p>Kami akan segera memberitahu kamu status reservasi ini.</p>
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
            <p><strong>Ruangan:</strong> {$booking->nama_ruangan}</p>
            <p><strong>Tanggal:</strong> {$booking->tanggal_penggunaan_ruang}</p>
            <p><strong>Waktu:</strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
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
            <p><strong>Ruangan:</strong> {$booking->nama_ruangan}</p>
            <p><strong>Tanggal:</strong> {$booking->tanggal_penggunaan_ruang}</p>
            <p><strong>Waktu:</strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
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

    public function sendInvitation(User $user, $booking, User $inviter): bool
    {
        $subject = 'Undangan Bergabung ke Booking | Library Booking App';
        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p><strong>{$inviter->nama}</strong> mengundang kamu untuk bergabung dalam booking ruangan.</p>
            <p><strong>Ruangan:</strong> {$booking->nama_ruangan}</p>
            <p><strong>Tanggal:</strong> {$booking->tanggal_penggunaan_ruang}</p>
            <p><strong>Waktu:</strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
            <p>Silakan login ke aplikasi untuk menerima atau menolak undangan ini.</p>
            <p>Terima kasih,<br>Library Booking App PNJ</p>
        ";

        return $this->send($user->email, $user->nama, $subject, $body);
    }

    public function sendJoinRequestApproved(User $user, $booking): bool
    {
        $subject = 'Permintaan Bergabung Disetujui | Library Booking App';
        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p>Permintaan kamu untuk bergabung ke booking telah disetujui oleh PIC.</p>
            <p><strong>Ruangan:</strong> {$booking->nama_ruangan}</p>
            <p><strong>Tanggal:</strong> {$booking->tanggal_penggunaan_ruang}</p>
            <p><strong>Waktu:</strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
            <p>Terima kasih,<br>Library Booking App PNJ</p>
        ";
        return $this->send($user->email, $user->nama, $subject, $body);
    }

    public function sendRescheduleApproved(User $user, $booking): bool
    {
        $subject = 'Reschedule Approved | Library Booking App';
        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p>Permintaan reschedule booking kamu telah disetujui.</p>
            <p><strong>Jadwal Baru:</strong></p>
            <p><strong>Tanggal:</strong> {$booking->tanggal_penggunaan_ruang}</p>
            <p><strong>Waktu:</strong> {$booking->waktu_mulai} - {$booking->waktu_selesai}</p>
            <p>Booking kamu sekarang berstatus <strong>Pending</strong> (menunggu verifikasi ulang jika diperlukan) atau <strong>Verified</strong>.</p>
            <p>Terima kasih,<br>Library Booking App PNJ</p>
        ";
        return $this->send($user->email, $user->nama, $subject, $body);
    }

    public function sendRescheduleRejected(User $user, $booking, string $reason): bool
    {
        $subject = 'Reschedule Rejected | Library Booking App';
        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p>Permintaan reschedule booking kamu ditolak.</p>
            <p><strong>Alasan:</strong> {$reason}</p>
            <p>Jadwal booking kamu tetap seperti sediakala (jika belum expired/cancel).</p>
            <p>Terima kasih,<br>Library Booking App PNJ</p>
        ";
        return $this->send($user->email, $user->nama, $subject, $body);
    }

    /**
     * Send warning notification email
     */
    public function sendWarningNotification(string $email, string $nama, string $warningType, string $reason = '', int $currentWarningCount = 0): bool
    {
        $subject = '⚠️ Peringatan Diterima | Library Booking App';

        $reasonText = $reason ? "<p><strong>Alasan:</strong> {$reason}</p>" : '';

        $suspensionWarning = '';
        if ($currentWarningCount >= 2) {
            $remaining = 3 - $currentWarningCount;
            if ($remaining <= 0) {
                $suspensionWarning = '
                    <p style="color: #dc2626; font-weight: bold;">
                        ⛔ Akun kamu telah disuspend karena mencapai 3 peringatan. 
                        Suspensi berlaku selama 7 hari.
                    </p>';
            } else {
                $suspensionWarning = "
                    <p style=\"color: #f59e0b; font-weight: bold;\">
                        ⚠️ Perhatian: Kamu tinggal {$remaining} peringatan lagi sebelum akun disuspend!
                    </p>";
            }
        }

        $body = "
            <p>Hai <strong>{$nama}</strong>,</p>
            <p>Kamu menerima peringatan dari sistem Library Booking App.</p>
            <p><strong>Jenis Peringatan:</strong> {$warningType}</p>
            {$reasonText}
            <p><strong>Jumlah Peringatan Saat Ini:</strong> {$currentWarningCount}</p>
            {$suspensionWarning}
            <p>Harap perhatikan aturan penggunaan ruangan perpustakaan untuk menghindari peringatan di masa depan.</p>
            <p>Jika kamu merasa ini adalah kesalahan, silakan hubungi admin.</p>
            <p>Terima kasih,<br>Library Booking App PNJ</p>
        ";

        return $this->send($email, $nama, $subject, $body);
    }

    /**
     * Send suspension notification email  
     */
    public function sendSuspensionNotification(string $email, string $nama, string $suspendUntil): bool
    {
        $subject = '⛔ Akun Disuspend | Library Booking App';
        $body = "
            <p>Hai <strong>{$nama}</strong>,</p>
            <p>Akun kamu telah <strong>disuspend</strong> karena mencapai 3 peringatan.</p>
            <p><strong>Suspensi berlaku hingga:</strong> {$suspendUntil}</p>
            <p>Selama masa suspensi, kamu tidak dapat:</p>
            <ul>
                <li>Membuat booking baru</li>
                <li>Bergabung ke booking orang lain</li>
                <li>Menggunakan fitur reservasi ruangan</li>
            </ul>
            <p>Setelah masa suspensi berakhir, akun kamu akan otomatis aktif kembali dan semua peringatan akan direset.</p>
            <p>Terima kasih,<br>Library Booking App PNJ</p>
        ";

        return $this->send($email, $nama, $subject, $body);
    }
}
