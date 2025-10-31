<?php
/** @var array $bookings */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2 class="text-lg font-medium">Manage Bookings</h2>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($m = App::$app->session->getFlash('dev_email')): ?>
  <p>[DEV MODE] <?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if (empty($bookings)): ?>
  <div class="mt-4 relative overflow-x-auto bg-gray-100 rounded-md shadow-md p-4">
    <p>No bookings found.</p>
  </div>
<?php else: ?>
  <div class="mt-4 relative overflow-x-auto bg-gray-100 rounded-md shadow-md p-4">
    <table class="w-full text-sm text-left text-gray-800 ">
      <thead class="text-sm text-gray-700 uppercase">
        <tr class=" *:px-6 *:py-3 odd:bg-gray-50">
          <th>ID</th>
          <th>User</th>
          <th>Room</th>
          <th>Date</th>
          <th>Time</th>
          <th>Participants</th>
          <th>Status</th>
          <th>Booking Code</th>
          <th>Actions</th>
        </tr>
      </thead>
      <?php foreach ($bookings as $booking): ?>
        <tr class="border-b border-gray-200 dark:border-gray-700 *:px-6 *:py-4 odd:bg-gray-50">
          <td><?= htmlspecialchars($booking['id']) ?></td>
          <td class="font-medium text-gray-900 whitespace-nowrap">
            <?= htmlspecialchars($booking['user_name']) ?><br>
            <small><?= htmlspecialchars($booking['user_email']) ?></small>
          </td>
          <td><?= htmlspecialchars($booking['room_title']) ?></td>
          <td><?= htmlspecialchars($booking['booking_date']) ?></td>
          <td><?= htmlspecialchars($booking['start_time']) ?> - <?= htmlspecialchars($booking['end_time']) ?></td>
          <td><?= htmlspecialchars($booking['participants']) ?></td>
          <td><?= htmlspecialchars(ucfirst($booking['status'])) ?></td>
          <td><?= htmlspecialchars($booking['booking_code'] ?? '-') ?></td>
          <td>
            <?php if ($booking['status'] === 'pending'): ?>
              <form action="/admin/bookings/validate" method="post">
                <?= Csrf::field() ?>
                <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['id']) ?>">
                <button type="submit">Validate</button>
              </form>
            <?php endif; ?>

            <?php if (in_array($booking['status'], ['pending', 'validated'])): ?>
              <form action="/admin/bookings/cancel" method="post">
                <?= Csrf::field() ?>
                <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['id']) ?>">
                <input type="hidden" name="reason" value="Cancelled by admin">
                <button type="submit" onclick="return confirm('Cancel this booking?')">Cancel</button>
              </form>
            <?php endif; ?>

            <?php if ($booking['status'] === 'active'): ?>
              <form action="/admin/bookings/complete" method="post">
                <?= Csrf::field() ?>
                <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['id']) ?>">
                <button type="submit">Complete</button>
              </form>
            <?php endif; ?>

            <?php if ($booking['status'] === 'completed' || $booking['status'] === 'cancelled'): ?>
              No actions
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>

  </div>
<?php endif; ?>

<div class="flex gap-2 mt-4 align-baseline items-center ">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
    class="size-4">
    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
  </svg>

  <a href="/admin" class=" hover:font-medium hover:underline text-gray-700">
    Back to Admin Dashboard
  </a>
</div>