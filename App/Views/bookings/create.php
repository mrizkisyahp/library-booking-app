<?php
/** @var \App\Models\Booking $booking */
/** @var \App\Models\Room $room */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2>Book Room: <?= htmlspecialchars($room->title) ?></h2>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<div>
  <p><strong>Room Capacity:</strong> <?= htmlspecialchars($room->capacity_min) ?> - <?= htmlspecialchars($room->capacity_max) ?> people</p>
  <?php if ($room->description): ?>
    <p><strong>Room Info:</strong> <?= htmlspecialchars($room->description) ?></p>
  <?php endif; ?>
</div>

<form action="/book?room_id=<?= $room->id ?>" method="post" enctype="multipart/form-data">
  <?= Csrf::field() ?>
  
  <input type="hidden" name="room_id" value="<?= $room->id ?>">

  <div>
    <label for="booking_date">Booking Date</label>
    <input id="booking_date" type="date" name="booking_date" value="<?= htmlspecialchars($booking->booking_date ?? '') ?>" />
    <?php if ($booking->hasError('booking_date')): ?>
      <p><?= htmlspecialchars($booking->getFirstError('booking_date')) ?></p>
    <?php endif; ?>
  </div>

  <div>
    <label for="start_time">Start Time</label>
    <input id="start_time" type="time" name="start_time" value="<?= htmlspecialchars($booking->start_time ?? '') ?>" />
    <?php if ($booking->hasError('start_time')): ?>
      <p><?= htmlspecialchars($booking->getFirstError('start_time')) ?></p>
    <?php endif; ?>
  </div>

  <div>
    <label for="end_time">End Time</label>
    <input id="end_time" type="time" name="end_time" value="<?= htmlspecialchars($booking->end_time ?? '') ?>" />
    <?php if ($booking->hasError('end_time')): ?>
      <p><?= htmlspecialchars($booking->getFirstError('end_time')) ?></p>
    <?php endif; ?>
  </div>

  <div>
    <label for="participants">Number of Participants</label>
    <input id="participants" type="number" name="participants" value="<?= htmlspecialchars($booking->participants ?? '') ?>" />
    <?php if ($booking->hasError('participants')): ?>
      <p><?= htmlspecialchars($booking->getFirstError('participants')) ?></p>
    <?php endif; ?>
    <small>Min: <?= $room->capacity_min ?>, Max: <?= $room->capacity_max ?></small>
  </div>

  <div>
    <label for="purpose">Purpose of Booking</label>
    <textarea id="purpose" name="purpose" rows="4"><?= htmlspecialchars($booking->purpose ?? '') ?></textarea>
    <?php if ($booking->hasError('purpose')): ?>
      <p><?= htmlspecialchars($booking->getFirstError('purpose')) ?></p>
    <?php endif; ?>
  </div>

  <div>
    <label for="image">Booking Image (optional)</label>
    <input type="file" id="image" name="image" accept="image/*">
    <small>Upload an image related to your booking (optional)</small>
  </div>

  <button type="submit">Submit Booking</button>
</form>

<p><a href="/rooms">Back to Room List</a></p>
