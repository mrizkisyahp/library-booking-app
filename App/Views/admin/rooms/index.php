<?php
/** @var array $rooms */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<div class="flex justify-between items-center">
  <h2 class="text-lg font-medium">
    Manage Rooms
  </h2>
  <div class="px-4 py-2 rounded-md shadow-md bg-primary text-white ">
    <a href="/admin/rooms/create">
      Create New Room
    </a>
  </div>
</div>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<div class="mt-4 relative overflow-x-auto bg-gray-100 rounded-md shadow-md p-4">
  <?php if (empty($rooms)): ?>
    <p>No rooms available.</p>
  <?php else: ?>
    <table class=" w-full text-sm text-left text-gray-800 ">
      <thead class="text-sm text-gray-700 uppercase">
        <tr class=" *:px-6 *:py-3 odd:bg-gray-50">
          <th>ID</th>
          <th>Title</th>
          <th>Capacity</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <?php foreach ($rooms as $room): ?>
        <tr class="border-b border-gray-200 dark:border-gray-700 *:px-6 *:py-4 even:bg-gray-50">
          <td scope="row"><?= $room->id ?></td>
          <td class="font-medium text-gray-900 whitespace-nowrap"><?= htmlspecialchars($room->title) ?></td>
          <td><?= $room->capacity_min ?> - <?= $room->capacity_max ?></td>
          <td><?= htmlspecialchars($room->status) ?></td>
          <td>
            <a href="/admin/rooms/edit?id=<?= $room->id ?>">Edit</a> |
            <form action="/admin/rooms/delete" method="post" style="display:inline;">
              <?= Csrf::field() ?>
              <input type="hidden" name="id" value="<?= htmlspecialchars($room->id) ?>">
              <button type="submit" onclick="return confirm('Delete this room?')">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</div>

<div class="flex gap-2 mt-4 align-baseline items-center ">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
    class="size-4">
    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
  </svg>

  <a href="/admin" class=" hover:font-medium hover:underline text-gray-700">
    Back to Admin Dashboard
  </a>
</div>