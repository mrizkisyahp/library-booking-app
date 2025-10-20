<?php
/** @var array $bookings */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2>My Bookings</h2>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if (empty($bookings)): ?>
  <p>You haven't made any bookings yet.</p>
  <p><a href="/rooms">Browse available rooms</a></p>
<?php else: ?>
  <table border="1">
    <tr>
      <th>Room</th>
      <th>Date</th>
      <th>Time</th>
      <th>Participants</th>
      <th>Purpose</th>
      <th>Status</th>
      <th>Booking Code</th>
      <th>Actions</th>
    </tr>
    <?php foreach ($bookings as $booking): ?>
    <tr>
      <td><?= htmlspecialchars($booking['room_title']) ?></td>
      <td><?= htmlspecialchars($booking['booking_date']) ?></td>
      <td><?= htmlspecialchars($booking['start_time']) ?> - <?= htmlspecialchars($booking['end_time']) ?></td>
      <td><?= htmlspecialchars($booking['participants']) ?></td>
      <td><?= htmlspecialchars(substr($booking['purpose'], 0, 50)) ?><?= strlen($booking['purpose']) > 50 ? '...' : '' ?></td>
      <td><?= htmlspecialchars(ucfirst($booking['status'])) ?></td>
      <td><?= htmlspecialchars($booking['booking_code'] ?? '-') ?></td>
      <td>
        <?php if ($booking['status'] === 'pending'): ?>
          Waiting for validation
        <?php elseif ($booking['status'] === 'validated'): ?>
          <a href="/checkin">Check-in Now</a>
        <?php elseif ($booking['status'] === 'active'): ?>
          <form action="/checkout" method="post">
            <?= Csrf::field() ?>
            <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['id']) ?>">
            <button type="submit">Check-out</button>
          </form>
        <?php elseif ($booking['status'] === 'completed'): ?>
          <a href="/feedback?booking_id=<?= $booking['id'] ?>">Give Feedback</a>
        <?php else: ?>
          -
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<p><a href="/dashboard">Back to Dashboard</a> | <a href="/rooms">Book Another Room</a> | <a href="/checkin">Check-in</a></p>
