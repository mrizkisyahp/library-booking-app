<?php
use App\Core\App;
use App\Core\Csrf;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;

/** @var Booking|null $booking */
/** @var User[] $users */
/** @var Room[] $rooms */

$statusOptions = $statusOptions ?? [];
$errors = $errors ?? [];
$old = $old ?? [];

if (!$booking) {
  echo '<p>Booking not found.</p>';
  echo '<p><a href="/admin/bookings">Back to list</a></p>';
  return;
}

$values = [
  'user_id' => $old['user_id'] ?? $booking->user_id,
  'ruangan_id' => $old['ruangan_id'] ?? $booking->ruangan_id,
  'tanggal_penggunaan_ruang' => $old['tanggal_penggunaan_ruang'] ?? $booking->tanggal_penggunaan_ruang,
  'waktu_mulai' => $old['waktu_mulai'] ?? $booking->waktu_mulai,
  'waktu_selesai' => $old['waktu_selesai'] ?? $booking->waktu_selesai,
  'tujuan' => $old['tujuan'] ?? $booking->tujuan,
  'status' => $old['status'] ?? $booking->status,
];
$statusLocked = ($booking->status === 'completed');
?>

<h1>Edit Booking</h1>
<p><a href="/admin/bookings">Back to list</a></p>

<?php if ($message = App::$app->session->getFlash('error')): ?>
  <p style="color: red;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($message = App::$app->session->getFlash('success')): ?>
  <p style="color: green;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if (!empty($errors)): ?>
  <div style="color: red;">
    <p>Please fix the following errors:</p>
    <ul>
      <?php foreach ($errors as $field => $message): ?>
        <li><?= htmlspecialchars(is_int($field) ? $message : $message) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" action="/admin/bookings/update">
  <?= csrf_field() ?>
  <input type="hidden" name="id_booking" value="<?= (int) $booking->id_booking ?>">

  <label>
    PIC
    <select name="user_id" required>
      <?php foreach ($users as $user): ?>
        <option value="<?= (int) $user->id_user ?>" <?= ((string) $values['user_id'] === (string) $user->id_user) ? 'selected' : '' ?>>
          <?= htmlspecialchars($user->nama) ?> (<?= htmlspecialchars($user->email) ?>)
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>
    Room
    <select name="ruangan_id" required>
      <?php foreach ($rooms as $room): ?>
        <option value="<?= (int) $room->id_ruangan ?>" <?= ((string) $values['ruangan_id'] === (string) $room->id_ruangan) ? 'selected' : '' ?>>
          <?= htmlspecialchars($room->nama_ruangan) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>
    Date
    <input type="date" name="tanggal_penggunaan_ruang"
      value="<?= htmlspecialchars((string) $values['tanggal_penggunaan_ruang']) ?>" required>
  </label>

  <label>
    Time Start
    <input type="time" name="waktu_mulai" value="<?= htmlspecialchars((string) $values['waktu_mulai']) ?>" required>
  </label>

  <label>
    Time End
    <input type="time" name="waktu_selesai" value="<?= htmlspecialchars((string) $values['waktu_selesai']) ?>" required>
  </label>

  <label>
    Purpose
    <textarea name="tujuan" rows="4" required><?= htmlspecialchars((string) $values['tujuan']) ?></textarea>
  </label>

  <label>
    Status
    <select name="status" <?= $statusLocked ? 'disabled' : '' ?>>
      <?php foreach ($statusOptions as $value => $label): ?>
        <option value="<?= htmlspecialchars($value) ?>" <?= ($values['status'] === $value) ? 'selected' : '' ?>>
          <?= htmlspecialchars($label) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <?php if ($statusLocked): ?>
      <input type="hidden" name="status" value="<?= htmlspecialchars((string) $values['status']) ?>">
      <p>Completed bookings cannot change status.</p>
    <?php endif; ?>
  </label>

  <button type="submit">Update</button>
</form>