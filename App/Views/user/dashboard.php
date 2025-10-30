<?php
/** @var array $stats */
/** @var array $bookings */
/** @var \App\Models\User $user */
use App\Core\App;
?>

<?php if ($m = App::$app->session->getFlash('warning')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<h2 class="text-lg">
  Welcome,
  <span class="font-semibold">
    <?= htmlspecialchars($user->nama) ?>!
  </span>
</h2>
<hr class="h-px py-2 text-gray-300">

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <div class="col-span-2 p-4 border-r border-r-gray-300">
    <h2 class="text-lg font-meium">
      Your Booking Statistics
    </h2>
    <div class=" grid grid-cols-1 sm:grid-cols-3 md:grid-cols-5 gap-4 mt-4">
      <div class="bg-gray-100 rounded-md shadow-md p-4 ">
        <p class="font-semibold text-lg">Total Booking</p>
        <div class="ml-2 text-lg font-medium ">
          <?= $stats['totalBookings'] ?? 0 ?>
        </div>
      </div>
      <div class="bg-gray-100 rounded-md shadow-md p-4 ">
        <p class="font-semibold text-lg">Pending</p>
        <div class="ml-2 text-lg font-medium ">
          <?= $stats['pendingBookings'] ?? 0 ?>
        </div>
      </div>
      <div class="bg-gray-100 rounded-md shadow-md p-4 ">
        <p class="font-semibold text-lg">Validated</p>
        <div class="ml-2 text-lg font-medium ">
          <?= $stats['validatedBookings'] ?? 0 ?>
        </div>
      </div>
      <div class="bg-gray-100 rounded-md shadow-md p-4 ">
        <p class="font-semibold text-lg">Active</p>
        <div class="ml-2 text-lg font-medium ">
          <?= $stats['activeBookings'] ?? 0 ?>
        </div>
      </div>
      <div class="bg-gray-100 rounded-md shadow-md p-4 ">
        <p class="font-semibold text-lg">Completed</p>
        <div class="ml-2 text-lg font-medium ">
          <?= $stats['completedBookings'] ?? 0 ?>
        </div>
      </div>
    </div>

    <div class="mt-8">
      <h2 class="text-lg font-meium">
        Recent Bookings
      </h2>
      <div class="bg-gray-100 p-4 rounded-md shadow-md mt-4">
        <?php if (empty($bookings)): ?>
          <p>No bookings yet. <a href="/rooms">Browse rooms</a> to make a booking.</p>
        <?php else: ?>
          <table border="1">
            <tr>
              <th>Room</th>
              <th>Date</th>
              <th>Time</th>
              <th>Status</th>
            </tr>
            <?php foreach ($bookings as $booking): ?>
              <tr>
                <td><?= htmlspecialchars($booking['room_title']) ?></td>
                <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                <td><?= htmlspecialchars($booking['start_time']) ?> - <?= htmlspecialchars($booking['end_time']) ?></td>
                <td><?= htmlspecialchars($booking['status']) ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
          <p><a href="/my-bookings">View all bookings</a></p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="mt-4">
    <h2 class="text-lg font-meium">
      Quick Links
    </h2>
    <div class="bg-gray-100 rounded-md shadow-md p-4 mt-4">
      <ul>
        <li><a href="/rooms">Browse Rooms</a></li>
        <li><a href="/my-bookings">My Bookings</a></li>
        <li><a href="/checkin">Check-in</a></li>
        <li><a href="/profile">Profile</a></li>
      </ul>
    </div>
  </div>
</div>