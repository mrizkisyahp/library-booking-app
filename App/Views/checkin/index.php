<?php
use App\Core\App;
use App\Core\Csrf;
?>

<h2>Check-in to Your Booking</h2>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<p>Enter your booking code to check-in. You can check-in up to 10 minutes before your booking start time.</p>

<form action="/checkin/verify" method="post">
  <?= Csrf::field() ?>
  
  <div>
    <label for="booking_code">Booking Code:</label>
    <input type="text" id="booking_code" name="booking_code" placeholder="e.g., BK20251020ABC123" required>
  </div>

  <button type="submit">Check-in</button>
</form>

<h3>Check-in Instructions:</h3>
<ul>
  <li>Check-in is available 10 minutes before your booking start time</li>
  <li>You must check-in before the booking start time</li>
  <li>If you don't check-in within 10 minutes after start time, your booking will be automatically cancelled</li>
  <li>Your booking code was sent to your email after validation</li>
</ul>

<p><a href="/my-bookings">View My Bookings</a> | <a href="/dashboard">Back to Dashboard</a></p>
