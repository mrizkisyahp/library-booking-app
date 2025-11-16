<?php
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;

/** @var array $feedback */
/** @var Booking|null $booking */
/** @var Room|null $room */
/** @var User|null $pic */
/** @var User|null $feedbackUser */
$members = $members ?? [];
?>

  <a href="/admin/feedback">Kembali ke daftar</a>

  <h1>Detail Feedback</h1>

  <section>
    <h2>Informasi Feedback</h2>
    <p><strong>User:</strong> <?= htmlspecialchars($feedbackUser?->nama ?? $feedback['user_name'] ?? '-') ?></p>
    <p><strong>Rating:</strong> <?= htmlspecialchars((string)($feedback['rating'] ?? '-')) ?></p>
    <p><strong>Komentar:</strong> <?= nl2br(htmlspecialchars($feedback['komentar'] ?? '-')) ?></p>
    <p><strong>Tanggal:</strong> <?= htmlspecialchars($feedback['created_at'] ?? '-') ?></p>
  </section>

  <section>
    <h2>Booking</h2>
    <?php if ($booking): ?>
      <p><strong>ID Booking:</strong> #<?= htmlspecialchars((string)$booking->id_booking) ?></p>
      <p><strong>Ruangan:</strong> <?= htmlspecialchars($room?->nama_ruangan ?? 'Tidak diketahui') ?></p>
      <p><strong>Tanggal Penggunaan:</strong> <?= htmlspecialchars($booking->tanggal_penggunaan_ruang ?? '-') ?></p>
      <p><strong>Waktu:</strong> <?= htmlspecialchars(($booking->waktu_mulai ?? '') . ' - ' . ($booking->waktu_selesai ?? '')) ?></p>
      <p><strong>PIC:</strong> <?= htmlspecialchars($pic?->nama ?? '-') ?></p>
    <?php else: ?>
      <p>Informasi booking tidak tersedia.</p>
    <?php endif; ?>
  </section>

  <section>
    <h2>Anggota</h2>
    <?php if (empty($members)): ?>
      <p>Tidak ada anggota yang tercatat.</p>
    <?php else: ?>
      <table border="1" cellpadding="6" cellspacing="0">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($members as $member): ?>
            <tr>
              <td><?= htmlspecialchars($member['nama'] ?? '-') ?></td>
              <td><?= htmlspecialchars($member['email'] ?? '-') ?></td>
              <td><?= !empty($member['is_owner']) ? 'PIC' : 'Anggota' ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>
