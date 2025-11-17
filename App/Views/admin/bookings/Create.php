<?php
use App\Core\App;
use App\Core\Csrf;
use App\Models\Room;
use App\Models\User;

/** @var User[] $users */
/** @var Room[] $rooms */

$errors = $errors ?? [];
$old = $old ?? [];
?>

<h1>Create Booking</h1>
<p><a href="/admin/bookings">Back to list</a></p>

<?php if ($message = App::$app->session->getFlash('error')): ?>
  <p style="color: red;"><?= htmlspecialchars($message) ?></p>
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

<form method="post" action="/admin/bookings/store">
  <?= Csrf::field() ?>
  <input type="hidden" name="status" value="verified">

  <label>
    PIC
    <select name="user_id" required>
      <option value="">Select User</option>
      <?php foreach ($users as $user): ?>
        <option value="<?= (int)$user->id_user ?>" <?= ((string)($old['user_id'] ?? '') === (string)$user->id_user) ? 'selected' : '' ?>>
          <?= htmlspecialchars($user->nama) ?> (<?= htmlspecialchars($user->email) ?>)
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>
    Room
    <select name="ruangan_id" required>
      <option value="">Select Room</option>
      <?php foreach ($rooms as $room): ?>
        <option value="<?= (int)$room->id_ruangan ?>" <?= ((string)($old['ruangan_id'] ?? '') === (string)$room->id_ruangan) ? 'selected' : '' ?>>
          <?= htmlspecialchars($room->nama_ruangan) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>

  <label>
    Date
    <input type="date" name="tanggal_penggunaan_ruang" value="<?= htmlspecialchars($old['tanggal_penggunaan_ruang'] ?? '') ?>" required>
  </label>

  <label>
    Time Start
    <input type="time" name="waktu_mulai" value="<?= htmlspecialchars($old['waktu_mulai'] ?? '') ?>" required>
  </label>

  <label>
    Time End
    <input type="time" name="waktu_selesai" value="<?= htmlspecialchars($old['waktu_selesai'] ?? '') ?>" required>
  </label>

  <label>
    Purpose
    <textarea name="tujuan" rows="4" required><?= htmlspecialchars($old['tujuan'] ?? '') ?></textarea>
  </label>

  <button type="submit">Save</button>
</form>
