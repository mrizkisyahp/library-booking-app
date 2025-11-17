<?php
use App\Core\App;
use App\Core\Csrf;
use App\Models\Room;
/** @var Room[] $rooms */

$filters = $filters ?? [];
$statusOptions = $statusOptions ?? [];
$roomTypes = [
  'Audio Visual',
  'Telekonferensi',
  'Kreasi dan Rekreasi',
  'Baca Kelompok',
  'Koleksi Bahasa Prancis',
  'Bimbingan & Konseling',
  'Ruang Rapat',
];
?>

  <h1>Room Management</h1>
  <p><a href="/admin/rooms/create">Create New Room</a></p>

  <?php if ($message = App::$app->session->getFlash('success')): ?>
    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <?php if ($message = App::$app->session->getFlash('error')): ?>
    <p style="color: red;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>



  <section>
    <h2>Filters</h2>
    <form method="get" action="/admin/rooms">
      <label>
        Keyword
        <input type="text" name="keyword" value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
      </label>
      <label>
        Room Type
        <select name="jenis_ruangan">
          <option value="">All Types</option>
          <?php foreach ($roomTypes as $type): ?>
            <option value="<?= htmlspecialchars($type) ?>" <?= ($filters['jenis_ruangan'] ?? '') === $type ? 'selected' : '' ?>>
              <?= htmlspecialchars($type) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>
        Status
        <select name="status_ruangan">
          <option value="">All</option>
          <?php foreach ($statusOptions as $status): ?>
            <option value="<?= htmlspecialchars($status) ?>" <?= ($filters['status_ruangan'] ?? '') === $status ? 'selected' : '' ?>>
              <?= htmlspecialchars(ucwords(str_replace('_', ' ', $status))) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
      <button type="submit">Apply</button>
      <a href="/admin/rooms">Reset</a>
    </form>
  </section>

  <section>
    <h2>Rooms</h2>
    <?php if (empty($rooms)): ?>
      <p>No rooms found.</p>
    <?php else: ?>
      <table border="1" cellpadding="6" cellspacing="0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Capacity</th>
            <th>Type</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rooms as $room): ?>
            <tr>
              <td><?= htmlspecialchars((string)$room->id_ruangan) ?></td>
              <td><?= htmlspecialchars($room->nama_ruangan) ?></td>
              <td><?= htmlspecialchars((string)$room->kapasitas_min) ?> - <?= htmlspecialchars((string)$room->kapasitas_max) ?></td>
              <td><?= htmlspecialchars($room->jenis_ruangan) ?></td>
              <td><?= htmlspecialchars($room->status_ruangan) ?></td>
              <td>
                <a href="/admin/rooms/show?id=<?= $room->id_ruangan ?>">View</a> |
                <a href="/admin/rooms/edit?id=<?= $room->id_ruangan ?>">Edit</a>
                <form method="post" action="/admin/rooms/delete" style="display: inline;">
                  <?= Csrf::field() ?>
                  <input type="hidden" name="id_ruangan" value="<?= $room->id_ruangan ?>">
                  <button type="submit" onclick="return confirm('Delete this room?')">Delete</button>
                </form>
                <?php if ($room->status_ruangan === 'available'): ?>
                  <form method="post" action="/admin/rooms/deactivate" style="display: inline;">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="id_ruangan" value="<?= $room->id_ruangan ?>">
                    <button type="submit">Deactivate</button>
                  </form>
                <?php else: ?>
                  <form method="post" action="/admin/rooms/activate" style="display: inline;">
                    <?= Csrf::field() ?>
                    <input type="hidden" name="id_ruangan" value="<?= $room->id_ruangan ?>">
                    <button type="submit">Activate</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>


<!-- Pagination -->
<div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
  <div class="flex items-center justify-between">
    <p class="text-sm text-slate-600">
      Showing <span class="font-semibold"><?= (($currentPage - 1) * $perPage) + 1 ?></span>
      to <span class="font-semibold"><?= min($currentPage * $perPage, $totalRooms) ?></span>
      of <span class="font-semibold"><?= $totalRooms ?></span> results
    </p>

    <div class="flex gap-2">
      <?php if ($currentPage > 1): ?>
        <a href="/admin/rooms?page=<?= $currentPage - 1 ?>"
           class="px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
          Previous
        </a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= ceil($totalRooms / $perPage); $i++): ?>
        <a href="/admin/rooms?page=<?= $i ?>"
           class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                  <?= $i === $currentPage ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'border border-slate-300 text-slate-700 hover:bg-slate-100' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>

      <?php if ($currentPage < ceil($totalRooms / $perPage)): ?>
        <a href="/admin/rooms?page=<?= $currentPage + 1 ?>"
           class="px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
          Next
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>