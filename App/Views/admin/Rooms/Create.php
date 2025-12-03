<?php
$validator = $validator ?? null;

$statusOptions = [
  'available' => 'Available',
  'unavailable' => 'Unavailable',
  'adminOnly' => 'Admin Only',
];
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

<h1>Create Room</h1>
<p><a href="/admin/rooms">Back to list</a></p>

<?php if ($message = flash('success')): ?>
  <p style="color: green;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($message = flash('error')): ?>
  <p style="color: red;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form action="/admin/rooms" method="post">
  <?= csrf_field() ?>
  <fieldset>
    <legend>Basic Information</legend>
    <label>
      Room Name
      <input type="text" name="nama_ruangan" value="">
    </label>
    <?php if ($validator?->hasError('nama_ruangan')): ?>
      <p style="color: red;"><?= htmlspecialchars($validator?->getFirstError('nama_ruangan')) ?></p>
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
    <?php if ($validator?->hasError('jenis_ruangan')): ?>
      <p style="color: red;"><?= htmlspecialchars($validator?->getFirstError('jenis_ruangan')) ?></p>
    <?php endif; ?>
  </fieldset>

  <fieldset>
    <legend>Capacity</legend>
    <label>
      Minimum Capacity
      <input type="number" name="kapasitas_min" min="1" value="">
    </label>
    <?php if ($validator?->hasError('kapasitas_min')): ?>
      <p style="color: red;"><?= htmlspecialchars($validator?->getFirstError('kapasitas_min')) ?></p>
    <?php endif; ?>

    <label>
      Maximum Capacity
      <input type="number" name="kapasitas_max" min="1" value="">
    </label>
    <?php if ($validator?->hasError('kapasitas_max')): ?>
      <p style="color: red;"><?= htmlspecialchars($validator?->getFirstError('kapasitas_max')) ?></p>
    <?php endif; ?>
  </fieldset>

  <fieldset>
    <legend>Description</legend>
    <label>
      Details
      <textarea name="deskripsi_ruangan" rows="4" cols="40"></textarea>
    </label>
    <?php if ($validator?->hasError('deskripsi_ruangan')): ?>
      <p style="color: red;"><?= htmlspecialchars($validator?->getFirstError('deskripsi_ruangan')) ?></p>
    <?php endif; ?>
  </fieldset>

  <fieldset>
    <legend>Status</legend>
    <label>
      Status
      <select name="status_ruangan">
        <?php foreach ($statusOptions as $status => $label): ?>
          <option value="<?= htmlspecialchars($status) ?>" <?= ($room->status_ruangan ?? '') === $status ? 'selected' : '' ?>>
            <?= htmlspecialchars($label) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>
    <?php if ($validator?->hasError('status_ruangan')): ?>
      <p style="color: red;"><?= htmlspecialchars($validator?->getFirstError('status_ruangan')) ?></p>
    <?php endif; ?>
  </fieldset>

  <button type="submit">Save Room</button>
  <a href="/admin/rooms">Cancel</a>
</form>