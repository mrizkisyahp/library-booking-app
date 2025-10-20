<?php
/** @var array $bookings */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2>Manage Bookings</h2>

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
  <p>No bookings found.</p>
<?php else: ?>
  <table border="1">
    <tr>
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
    <?php foreach ($bookings as $booking): ?>
    <tr>
      <td><?= htmlspecialchars($booking['id']) ?></td>
      <td>
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
<?php endif; ?>

<p><a href="/admin">Back to Admin Dashboard</a></p>
