<?php
/** @var array $rooms */
use App\Core\App;
/** @var \App\Models\User $user */ $user = App::$app->user; 
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2 class="text-lg font-medium">Available Rooms</h2>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if (empty($rooms)): ?>
    <div class="mt-4 relative overflow-x-auto bg-gray-100 rounded-md shadow-md p-4">
  <p>No rooms available at the moment.</p>
    </div>
<?php else: ?>
    <div class="mt-4 relative overflow-x-auto bg-gray-100 rounded-md shadow-md p-4">
  <table class="w-full text-sm text-left text-gray-800 ">
    <thead class="text-sm text-gray-700 uppercase">
    <tr class=" *:px-6 *:py-3 odd:bg-gray-50">
      <th>Room Name</th>
      <th>Capacity</th>
      <th>Status</th>
      <th>Action</th>
    </tr>
    </thead>
    <?php foreach ($rooms as $room): ?>
    <tr class="border-b border-gray-200 dark:border-gray-700 *:px-6 *:py-4 even:bg-gray-50">
      <td class="font-medium text-gray-900 whitespace-nowrap"><?= htmlspecialchars($room->title) ?></td>
      <td><?= htmlspecialchars($room->capacity_min) ?> - <?= htmlspecialchars($room->capacity_max) ?> people</td>
      <td><?= htmlspecialchars($room->status) ?></td>
      <td>
        <a href="/room?id=<?= $room->id ?>">View Details</a> |
        <a href="/book?room_id=<?= $room->id ?>">Book Now</a>
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

  <a href="<?= $user->role === 'admin' ? '/admin' : '/dashboard' ?>" class=" hover:font-medium hover:underline text-gray-700">
    Back to Dashboard
  </a>
</div>
