<?php
/** @var array $rooms */
use App\Core\App;
use App\Core\Csrf;
?>

<!-- Disini za buat styling css sama atur2 margin lah -->

<h2>Manage Rooms</h2>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<?php if ($m = App::$app->session->getFlash('error')): ?>
  <p><?= htmlspecialchars($m) ?></p>
<?php endif; ?>

<p><a href="/admin/rooms/create">Create New Room</a></p>

<?php if (empty($rooms)): ?>
  <p>No rooms available.</p>
<?php else: ?>
  <table border="1">
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Capacity</th>
      <th>Status</th>
      <th>Actions</th>
    </tr>
    <?php foreach ($rooms as $room): ?>
    <tr>
      <td><?= $room->id ?></td>
      <td><?= htmlspecialchars($room->title) ?></td>
      <td><?= $room->capacity_min ?> - <?= $room->capacity_max ?></td>
      <td><?= htmlspecialchars($room->status) ?></td>
      <td>
        <a href="/admin/rooms/edit?id=<?= $room->id ?>">Edit</a> |
        <form action="/admin/rooms/delete" method="post" style="display:inline;">
          <?= Csrf::field() ?>
          <input type="hidden" name="id" value="<?= htmlspecialchars($room->id) ?>">
          <button type="submit" onclick="return confirm('Delete this room?')">Delete</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<p><a href="/admin">Back to Admin Dashboard</a></p>
