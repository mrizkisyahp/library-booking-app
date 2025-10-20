<?php
/** @var array $stats */
/** @var array $bookings */
/** @var \App\Models\User $user */
use App\Core\App;
?>

<?php if ($m = App::$app->session->getFlash('warning')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<h2>Welcome, <?= htmlspecialchars($user->nama) ?>!</h2>

<h3>Your Booking Statistics</h3>
<table border="1">
  <tr>
    <th>Total Bookings</th>
    <th>Pending</th>
    <th>Validated</th>
    <th>Active</th>
    <th>Completed</th>
  </tr>
  <tr>
    <td><?= $stats['totalBookings'] ?? 0 ?></td>
    <td><?= $stats['pendingBookings'] ?? 0 ?></td>
    <td><?= $stats['validatedBookings'] ?? 0 ?></td>
    <td><?= $stats['activeBookings'] ?? 0 ?></td>
    <td><?= $stats['completedBookings'] ?? 0 ?></td>
  </tr>
</table>

<h3>Recent Bookings</h3>
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

<h3>Quick Links</h3>
<ul>
  <li><a href="/rooms">Browse Rooms</a></li>
  <li><a href="/my-bookings">My Bookings</a></li>
  <li><a href="/checkin">Check-in</a></li>
  <li><a href="/profile">Profile</a></li>
</ul>
