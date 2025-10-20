<?php
/** @var \App\Models\Room $room */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2>Create New Room</h2>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<form action="/admin/rooms/create" method="post" enctype="multipart/form-data">
  <?= Csrf::field() ?>

  <div>
    <label for="title">Room Title</label>
    <input id="title" type="text" name="title" value="<?= htmlspecialchars($room->title ?? '') ?>" />
    <?php if ($room->hasError('title')): ?>
      <p><?= htmlspecialchars($room->getFirstError('title')) ?></p>
    <?php endif; ?>
  </div>

  <div>
    <label for="capacity_min">Minimum Capacity</label>
    <input id="capacity_min" type="number" name="capacity_min" value="<?= htmlspecialchars($room->capacity_min ?? '') ?>" />
    <?php if ($room->hasError('capacity_min')): ?>
      <p><?= htmlspecialchars($room->getFirstError('capacity_min')) ?></p>
    <?php endif; ?>
  </div>

  <div>
    <label for="capacity_max">Maximum Capacity</label>
    <input id="capacity_max" type="number" name="capacity_max" value="<?= htmlspecialchars($room->capacity_max ?? '') ?>" />
    <?php if ($room->hasError('capacity_max')): ?>
      <p><?= htmlspecialchars($room->getFirstError('capacity_max')) ?></p>
    <?php endif; ?>
  </div>

  <div>
    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4"><?= htmlspecialchars($room->description ?? '') ?></textarea>
    <?php if ($room->hasError('description')): ?>
      <p><?= htmlspecialchars($room->getFirstError('description')) ?></p>
    <?php endif; ?>
  </div>

  <div>
    <label for="image">Room Image</label>
    <input type="file" id="image" name="image" accept="image/*">
  </div>

  <div>
    <label for="status">Status</label>
    <select id="status" name="status" required>
      <option value="available" <?= $room->status === 'available' ? 'selected' : '' ?>>Available</option>
      <option value="maintenance" <?= $room->status === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
    </select>
  </div>

  <button type="submit">Create Room</button>
</form>

<p><a href="/admin/rooms">Back to Room List</a></p>
