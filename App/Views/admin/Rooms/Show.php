<?php
use App\Core\App;
use App\Core\Csrf;
use App\Models\Room;
/** @var Room $room */
?>

  <h1>Room Detail</h1>
  <p><a href="/admin/rooms">Back to list</a></p>

  <?php if ($message = App::$app->session->getFlash('success')): ?>
    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <?php if ($message = App::$app->session->getFlash('error')): ?>
    <p style="color: red;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <section>
    <h2>Information</h2>
    <p><strong>ID:</strong> <?= htmlspecialchars((string)$room->id_ruangan) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($room->nama_ruangan) ?></p>
    <p><strong>Type:</strong> <?= htmlspecialchars($room->jenis_ruangan) ?></p>
    <p><strong>Capacity:</strong> <?= htmlspecialchars((string)$room->kapasitas_min) ?> - <?= htmlspecialchars((string)$room->kapasitas_max) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($room->status_ruangan) ?></p>
    <p><strong>Description:</strong></p>
    <p><?= nl2br(htmlspecialchars($room->deskripsi_ruangan)) ?></p>
  </section>

  <section>
    <h2>Actions</h2>
    <p>
      <a href="/admin/rooms/edit?id=<?= $room->id_ruangan ?>">Edit Room</a>
    </p>
    <form method="post" action="/admin/rooms/delete" onsubmit="return confirm('Delete this room?');">
      <?= Csrf::field() ?>
      <input type="hidden" name="id_ruangan" value="<?= $room->id_ruangan ?>">
      <button type="submit">Delete Room</button>
    </form>
    <?php if ($room->status_ruangan === 'available'): ?>
      <form method="post" action="/admin/rooms/deactivate">
        <?= Csrf::field() ?>
        <input type="hidden" name="id_ruangan" value="<?= $room->id_ruangan ?>">
        <button type="submit">Deactivate Room</button>
      </form>
    <?php else: ?>
      <form method="post" action="/admin/rooms/activate">
        <?= Csrf::field() ?>
        <input type="hidden" name="id_ruangan" value="<?= $room->id_ruangan ?>">
        <button type="submit">Activate Room</button>
      </form>
    <?php endif; ?>
  </section>

