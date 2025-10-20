<?php
/** @var \App\Models\Room $room */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2>Edit Room</h2>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<form action="/admin/rooms/edit?id=<?= $room->id ?>" method="post">
  <?= Csrf::field() ?>

  <div class="mb-4">
    <label class="block text-sm font-medium <?= $room->hasError('title') ? 'text-red-700' : 'text-gray-700' ?> mb-2" for="title">Room Title</label>
    <input id="title" type="text" name="title" value="<?= htmlspecialchars($room->title ?? '') ?>" class="w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $room->hasError('title') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
    <?php if ($room->hasError('title')): ?>
      <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($room->getFirstError('title')) ?></p>
    <?php endif; ?>
  </div>

  <div class="mb-4">
    <label class="block text-sm font-medium <?= $room->hasError('capacity_min') ? 'text-red-700' : 'text-gray-700' ?> mb-2" for="capacity_min">Minimum Capacity</label>
    <input id="capacity_min" type="number" name="capacity_min" value="<?= htmlspecialchars($room->capacity_min ?? '') ?>" class="w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $room->hasError('capacity_min') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
    <?php if ($room->hasError('capacity_min')): ?>
      <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($room->getFirstError('capacity_min')) ?></p>
    <?php endif; ?>
  </div>

  <div class="mb-4">
    <label class="block text-sm font-medium <?= $room->hasError('capacity_max') ? 'text-red-700' : 'text-gray-700' ?> mb-2" for="capacity_max">Maximum Capacity</label>
    <input id="capacity_max" type="number" name="capacity_max" value="<?= htmlspecialchars($room->capacity_max ?? '') ?>" class="w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $room->hasError('capacity_max') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>" />
    <?php if ($room->hasError('capacity_max')): ?>
      <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($room->getFirstError('capacity_max')) ?></p>
    <?php endif; ?>
  </div>

  <div class="mb-4">
    <label class="block text-sm font-medium <?= $room->hasError('description') ? 'text-red-700' : 'text-gray-700' ?> mb-2" for="description">Description</label>
    <textarea id="description" name="description" rows="4" class="w-full px-3 py-2 rounded-lg border shadow-sm focus:outline-none focus:ring-2 <?= $room->hasError('description') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-indigo-500 focus:border-indigo-500' ?>"><?= htmlspecialchars($room->description ?? '') ?></textarea>
    <?php if ($room->hasError('description')): ?>
      <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($room->getFirstError('description')) ?></p>
    <?php endif; ?>
  </div>

  <div>
    <label for="status">Status</label>
    <select id="status" name="status" required>
      <option value="available" <?= $room->status === 'available' ? 'selected' : '' ?>>Available</option>
      <option value="maintenance" <?= $room->status === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
    </select>
  </div>

  <button type="submit">Update Room</button>
</form>

<p><a href="/admin/rooms">Back to Room List</a></p>
