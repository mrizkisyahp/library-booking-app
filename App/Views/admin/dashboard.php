<?php
/** @var array $stats */
/** @var array $recentBookings */
/** @var array $roomUsage */
use App\Core\App;
?>

<h2>Admin Dashboard</h2>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<h3>Statistics</h3>
<table border="1">
  <tr>
    <th>Total Bookings</th>
    <th>Pending</th>
    <th>Active</th>
    <th>Completed</th>
    <th>Total Rooms</th>
    <th>Available Rooms</th>
    <th>Total Users</th>
    <th>Verified Users</th>
  </tr>
  <tr>
    <td><?= $stats['totalBookings'] ?></td>
    <td><?= $stats['pendingBookings'] ?></td>
    <td><?= $stats['activeBookings'] ?></td>
    <td><?= $stats['completedBookings'] ?></td>
    <td><?= $stats['totalRooms'] ?></td>
    <td><?= $stats['availableRooms'] ?></td>
    <td><?= $stats['totalUsers'] ?></td>
    <td><?= $stats['verifiedUsers'] ?></td>
  </tr>
</table>

<h3>Recent Bookings</h3>
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

<h3>Room Usage</h3>
<?php if (empty($roomUsage)): ?>
  <p>No room usage data.</p>
<?php else: ?>
  <table border="1">
    <tr>
      <th>Room</th>
      <th>Total Bookings</th>
      <th>Usage %</th>
    </tr>
    <?php 
    $totalRooms = count($roomUsage);
    foreach ($roomUsage as $usage): 
      $usagePercentage = $totalRooms > 0 ? round(($usage['booking_count'] > 0 ? 1 : 0) / $totalRooms * 100, 2) : 0;
    ?>
    <tr>
      <td><?= htmlspecialchars($usage['title']) ?></td>
      <td><?= $usage['booking_count'] ?></td>
      <td><?= $usagePercentage ?>%</td>
    </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<h3>Quick Links</h3>
<ul>
  <li><a href="/admin/bookings">Manage Bookings</a></li>
  <li><a href="/admin/rooms">Manage Rooms</a></li>
  <li><a href="/admin/users">Manage Users</a></li>
  <li><a href="/admin/reports">Generate Reports</a></li>
</ul>
