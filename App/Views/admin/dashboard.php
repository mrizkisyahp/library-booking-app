<?php
/** @var array $stats */
/** @var array $recentBookings */
/** @var array $roomUsage */
use App\Core\App;
?>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<h2 class="text-lg font-bold">Admin Dashboard</h2>
<hr class="h-px py-2 text-gray-300">

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <div class="col-span-2 p-4 border-r border-r-gray-300">
    <h2 class="text-lg font-medium">
      Your Booking Statistics
    </h2>
    <div class=" grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-4">
      <div class="bg-gray-100 rounded-md shadow-md p-4 ">
        <p class="font-semibold text-lg">Total Booking</p>
        <div class="ml-2 text-lg font-medium ">
          <?= $stats['totalBookings'] ?>
        </div>
      </div>
      <div class="bg-gray-100 rounded-md shadow-md p-4 ">
        <p class="font-semibold text-lg">Pending</p>
        <div class="ml-2 text-lg font-medium ">
          <?= $stats['pendingBookings'] ?>
        </div>
      </div>
      <div class="bg-gray-100 rounded-md shadow-md p-4 ">
        <p class="font-semibold text-lg">Active</p>
        <div class="ml-2 text-lg font-medium ">
          <?= $stats['activeBookings'] ?>
        </div>
      </div>
      <div class="bg-gray-100 rounded-md shadow-md p-4 ">
        <p class="font-semibold text-lg">Completed</p>
        <div class="ml-2 text-lg font-medium ">
          <?= $stats['completedBookings'] ?>
        </div>
      </div>
      <div class="bg-gray-100 rounded-md shadow-md p-4 ">
        <p class="font-semibold text-lg">Total Rooms</p>
        <div class="ml-2 text-lg font-medium ">
          <?= $stats['totalRooms'] ?>
        </div>
      </div>
      <div class="bg-gray-100 rounded-md shadow-md p-4 ">
        <p class="font-semibold text-lg">Available Rooms</p>
        <div class="ml-2 text-lg font-medium ">
          <?= $stats['availableRooms'] ?>
        </div>
      </div>
      <div class="bg-gray-100 rounded-md shadow-md p-4 ">
        <p class="font-semibold text-lg">Total Users</p>
        <div class="ml-2 text-lg font-medium ">
          <?= $stats['totalUsers'] ?>
        </div>
      </div>
      <div class="bg-gray-100 rounded-md shadow-md p-4 ">
        <p class="font-semibold text-lg">Verified Users</p>
        <div class="ml-2 text-lg font-medium ">
          <?= $stats['verifiedUsers'] ?>
        </div>
      </div>
    </div>

    <div class="mt-8">
      <h2 class="text-lg">
        Recent Bookings
      </h2>
      <div class="bg-gray-100 p-4 rounded-md shadow-md mt-4">
        <?php if (empty($recentBookings)): ?>
          <p>No recent bookings.</p>
        <?php else: ?>
          <table border="1">
            <tr>
              <th>User</th>
              <th>Room</th>
              <th>Date</th>
              <th>Time</th>
              <th>Status</th>
            </tr>
            <?php foreach ($recentBookings as $booking): ?>
              <tr>
                <td><?= htmlspecialchars($booking['user_name']) ?></td>
                <td><?= htmlspecialchars($booking['room_title']) ?></td>
                <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                <td><?= htmlspecialchars($booking['start_time']) ?> - <?= htmlspecialchars($booking['end_time']) ?></td>
                <td><?= htmlspecialchars($booking['status']) ?></td>
              </tr>
            <?php endforeach; ?>
          </table>
        <?php endif; ?>
      </div>
    </div>

    <div class="mt-8">
      <h2 class="text-lg font-medium">
        Rooms Usage
      </h2>
      <div class="mt-4 relative overflow-x-auto bg-gray-100 rounded-md shadow-md p-4">
        <?php if (empty($roomUsage)): ?>
          <p>No room usage data.</p>
        <?php else: ?>
          <table class=" w-full text-sm text-left text-gray-800 ">
            <thead class="text-sm text-gray-700 uppercase">
              <tr class=" *:px-6 *:py-3">
              <th class="bg-gray-50">Room</th>
              <th>Total Bookings</th>
              <th class="bg-gray-50">Usage (%)</th>
            </tr>
            </thead>
            <?php
            $totalRooms = count($roomUsage);
            foreach ($roomUsage as $usage):
              $usagePercentage = $totalRooms > 0 ? round(($usage['booking_count'] > 0 ? 1 : 0) / $totalRooms * 100, 2) : 0;
              ?>
              <tr class="border-b border-gray-200 dark:border-gray-700 *:px-6 *:py-4">
                <td scope="row" class="font-medium text-gray-900 whitespace-nowrap bg-gray-50"><?= htmlspecialchars($usage['title']) ?></td>
                <td><?= $usage['booking_count'] ?></td>
                <td class="bg-gray-50 text-gray-900"><?= $usagePercentage ?>%</td>
              </tr>
            <?php endforeach; ?>
          </table>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <div class="mt-4">
    <h2 class="text-lg">
      Quick Links
    </h2>
    <div class="bg-gray-100 rounded-md shadow-md p-4 mt-4">
      <ul class=" list-disc list-inside *:hover:underline text-gray-700">
        <li><a href="/admin/bookings">Manage Bookings</a></li>
        <li><a href="/admin/rooms">Manage Rooms</a></li>
        <li><a href="/admin/users">Manage Users</a></li>
        <li><a href="/admin/reports">Generate Reports</a></li>
      </ul>
    </div>
  </div>
</div>