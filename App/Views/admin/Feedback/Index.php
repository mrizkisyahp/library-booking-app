<?php
use App\Core\Csrf;

$filters = $filters ?? [];
$feedback = $feedback ?? [];
?>

  <h1>Feedback Pengguna</h1>

  <section>
    <h2>Pencarian</h2>
    <form method="get" action="/admin/feedback">
      <fieldset>
        <label>
          Booking ID
          <input type="number" name="booking_id" value="<?= htmlspecialchars($filters['booking_id'] ?? '') ?>">
        </label>
        <label>
          User ID
          <input type="number" name="user_id" value="<?= htmlspecialchars($filters['user_id'] ?? '') ?>">
        </label>
        <label>
          Room ID
          <input type="number" name="room_id" value="<?= htmlspecialchars($filters['room_id'] ?? '') ?>">
        </label>
        <label>
          Rating Min
          <input type="number" name="rating_min" min="1" max="5" value="<?= htmlspecialchars($filters['rating_min'] ?? '') ?>">
        </label>
        <label>
          Rating Max
          <input type="number" name="rating_max" min="1" max="5" value="<?= htmlspecialchars($filters['rating_max'] ?? '') ?>">
        </label>
        <label>
          Dari Tanggal
          <input type="date" name="date_start" value="<?= htmlspecialchars($filters['date_start'] ?? '') ?>">
        </label>
        <label>
          Sampai Tanggal
          <input type="date" name="date_end" value="<?= htmlspecialchars($filters['date_end'] ?? '') ?>">
        </label>
        <button type="submit">Terapkan</button>
        <a href="/admin/feedback">Reset</a>
      </fieldset>
    </form>
  </section>

  <section>
    <h2>Daftar Feedback</h2>
    <?php if (empty($feedback)): ?>
      <p>Tidak ada feedback ditemukan.</p>
    <?php else: ?>
      <table border="1" cellpadding="6" cellspacing="0">
        <thead>
          <tr>
            <th>User</th>
            <th>Booking</th>
            <th>Ruangan</th>
            <th>Rating</th>
            <th>Tanggal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($feedback as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['user_name'] ?? '-') ?></td>
              <td>#<?= htmlspecialchars((string)$row['booking_id']) ?></td>
              <td><?= htmlspecialchars($row['nama_ruangan'] ?? '-') ?></td>
              <td><?= htmlspecialchars((string)$row['rating']) ?></td>
              <td><?= htmlspecialchars($row['created_at'] ?? '-') ?></td>
              <td><a href="/admin/feedback/detail?id=<?= (int)$row['id_feedback'] ?>">Detail</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

