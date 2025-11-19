<?php
use App\Core\App;
use App\Core\Csrf;
use App\Models\Room;
/** @var Room $room */
?>
<div class="p-6">
  <div class="mb-8 flex flex-col md:flex-row justify-between items-center">
    <div class="flex gap-4 items-center">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        class="lucide lucide-move-left-icon lucide-move-left">
        <path d="M6 8L2 12L6 16" />
        <path d="M2 12H22" />
      </svg>
      <a href="/admin/feedback">Kembali ke daftar</a>
    </div>
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Detail Ruangan</h1>
    <!-- empty div -->
    <div></div>
  </div>

  <!-- Flash Messages -->
  <?php if ($message = App::$app->session->getFlash('success')): ?>
    <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <?php if ($message = App::$app->session->getFlash('error')): ?>
    <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <section class="bg-white shadow rounded-lg p-6 mb-8 border border-gray-100 grow h-max">
    <h2>Information</h2>
    <p><strong>ID:</strong> <?= htmlspecialchars((string) $room->id_ruangan) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($room->nama_ruangan) ?></p>
    <p><strong>Type:</strong> <?= htmlspecialchars($room->jenis_ruangan) ?></p>
    <p><strong>Capacity:</strong> <?= htmlspecialchars((string) $room->kapasitas_min) ?> -
      <?= htmlspecialchars((string) $room->kapasitas_max) ?></p>
    <p><strong>Status:</strong> <?= htmlspecialchars($room->status_ruangan) ?></p>
    <p><strong>Description:</strong></p>
    <p><?= nl2br(htmlspecialchars($room->deskripsi_ruangan)) ?></p>
  </section>

  <section class="bg-white shadow rounded-lg p-6 mb-8 border border-gray-100 grow h-max">
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
</div>