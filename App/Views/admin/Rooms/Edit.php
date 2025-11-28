<?php
use App\Core\App;
use App\Core\Csrf;
use App\Models\Room;
/** @var Room $room */

$statusOptions = $statusOptions ?? [];
$roomTypes = [
  'Audio Visual',
  'Telekonferensi',
  'Kreasi dan Rekreasi',
  'Baca Kelompok',
  'Koleksi Bahasa Prancis',
  'Bimbingan & Konseling',
  'Ruang Rapat',
];
?>

<h1>Edit Room</h1>
<p><a href="/admin/rooms">Back to list</a></p>

<?php if ($message = App::$app->session->getFlash('success')): ?>
  <p style="color: green;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($message = App::$app->session->getFlash('error')): ?>
  <p style="color: red;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form action="/admin/rooms/update" method="post">
  <?= csrf_field() ?>
  <input type="hidden" name="id_ruangan" value="<?= htmlspecialchars((string) $room->id_ruangan) ?>">

  <fieldset>
    <legend>Basic Information</legend>
    <label>
      Room Name
      <input type="text" name="nama_ruangan" value="<?= htmlspecialchars($room->nama_ruangan ?? '') ?>">
    </label>
    <?php if ($room->hasError('nama_ruangan')): ?>
      <p style="color: red;"><?= htmlspecialchars($room->getFirstError('nama_ruangan')) ?></p>
    <?php endif; ?>

    <label>
      Room Type
      <select name="jenis_ruangan">
        <option value="">-- Pilih Jenis --</option>
        <?php foreach ($roomTypes as $type): ?>
          <option value="<?= htmlspecialchars($type) ?>" <?= ($room->jenis_ruangan ?? '') === $type ? 'selected' : '' ?>>
            <?= htmlspecialchars($type) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <?php if ($room->hasError('jenis_ruangan')): ?>
      <p style="color: red;"><?= htmlspecialchars($room->getFirstError('jenis_ruangan')) ?></p>
    <?php endif; ?>
  </fieldset>

  <fieldset>
    <legend>Capacity</legend>
    <label>
      Minimum Capacity
      <input type="number" name="kapasitas_min" min="1"
        value="<?= htmlspecialchars((string) ($room->kapasitas_min ?? '')) ?>">
    </label>
    <?php if ($room->hasError('kapasitas_min')): ?>
      <p style="color: red;"><?= htmlspecialchars($room->getFirstError('kapasitas_min')) ?></p>
    <?php endif; ?>

    <label>
      Maximum Capacity
      <input type="number" name="kapasitas_max" min="1"
        value="<?= htmlspecialchars((string) ($room->kapasitas_max ?? '')) ?>">
    </label>
    <?php if ($room->hasError('kapasitas_max')): ?>
      <p style="color: red;"><?= htmlspecialchars($room->getFirstError('kapasitas_max')) ?></p>
    <?php endif; ?>
  </fieldset>

  <fieldset>
    <legend>Description</legend>
    <label>
      Details
      <textarea name="deskripsi_ruangan" rows="4"
        cols="40"><?= htmlspecialchars($room->deskripsi_ruangan ?? '') ?></textarea>
    </label>
    <?php if ($room->hasError('deskripsi_ruangan')): ?>
      <p style="color: red;"><?= htmlspecialchars($room->getFirstError('deskripsi_ruangan')) ?></p>
    <?php endif; ?>
  </fieldset>

  <fieldset>
    <legend>Status</legend>
    <label>
      Status
      <select name="status_ruangan">
        <?php foreach ($statusOptions as $status): ?>
          <option value="<?= htmlspecialchars($status) ?>" <?= ($room->status_ruangan ?? '') === $status ? 'selected' : '' ?>>
            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $status))) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <?php if ($room->hasError('status_ruangan')): ?>
      <p style="color: red;"><?= htmlspecialchars($room->getFirstError('status_ruangan')) ?></p>
    <?php endif; ?>
  </fieldset>

  <button type="submit">Update Room</button>
  <a href="/admin/rooms">Cancel</a>
</form>