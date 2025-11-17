<?php

use App\Core\Csrf;
/** @var array $bookings */

// Status badge colors
$statusColors = [
  'draft' => 'bg-gray-100 text-gray-800',
  'pending' => 'bg-yellow-100 text-yellow-800',
  'verified' => 'bg-blue-100 text-blue-800',
  'active' => 'bg-emerald-100 text-emerald-800',
  'completed' => 'bg-green-100 text-green-800',
  'cancelled' => 'bg-red-100 text-red-800',
  'expired' => 'bg-gray-100 text-gray-600',
  'no_show' => 'bg-orange-100 text-orange-800'
];
?>

<div class="p-6">
  <!-- Header -->
  <div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-900 mb-2">Manajemen Booking</h2>
    <p class="text-gray-600">Kelola dan verifikasi booking ruangan</p>
  </div>

  <?php if (empty($bookings)): ?>
    <!-- Empty State -->
    <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-100">
      <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
      </svg>
      <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Booking</h3>
      <p class="text-gray-600">Belum ada booking yang perlu dikelola saat ini.</p>
    </div>
  <?php else: ?>
    <!-- Bookings Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-linear-to-r from-emerald-100 to-teal-50">
            <tr class=" *:px-6 *:py-4  *:text-left *:text-xs *:font-bold *:text-gray-800 *:uppercase *:tracking-wider *:whitespace-nowrap">
              <th class="">User</th>
              <th class="">Ruangan</th>
              <th class="">Tanggal & Waktu</th>
              <th class="">Status</th>
              <th class="">Feedback</th>
              <th class="">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php foreach ($bookings as $booking): ?>
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 capitalize">
                  <?= htmlspecialchars($booking->nama) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  <?= htmlspecialchars($booking->nama_ruangan) ?>
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  <div class="font-medium"><?= htmlspecialchars($booking->tanggal_penggunaan_ruang) ?></div>
                  <div class="text-gray-500"><?= htmlspecialchars($booking->waktu_mulai) ?> - <?= htmlspecialchars($booking->waktu_selesai) ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow <?= $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800' ?>">
                    <?= htmlspecialchars(ucfirst($booking->status)) ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-sm">
                  <?php if (!empty($booking->id_feedback)) : ?>
                    <a href="/admin/feedback/detail?id=<?= (int)$booking->id_feedback ?>" class="text-emerald-600 hover:text-emerald-700 hover:underline font-semibold text-sm">Lihat Feedback</a>
                  <?php else: ?>
                    <span class="text-gray-400 text-sm">Tidak ada</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-y-2">
                  <a href="/admin/bookings/detail?id=<?= (int)$booking->id_booking ?>"
                     class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    Detail
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Pagination -->
<div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
  <div class="flex items-center justify-between">
    <p class="text-sm text-slate-600">
      Showing <span class="font-semibold"><?= (($currentPage - 1) * $perPage) + 1 ?></span>
      to <span class="font-semibold"><?= min($currentPage * $perPage, $totalBookings) ?></span>
      of <span class="font-semibold"><?= $totalBookings ?></span> results
    </p>

    <div class="flex gap-2">
      <?php if ($currentPage > 1): ?>
        <a href="/admin/bookings?page=<?= $currentPage - 1 ?>"
           class="px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
          Previous
        </a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= ceil($totalBookings / $perPage); $i++): ?>
        <a href="/admin/bookings?page=<?= $i ?>"
           class="px-3 py-2 rounded-lg text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500
                  <?= $i === $currentPage ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'border border-slate-300 text-slate-700 hover:bg-slate-100' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>

      <?php if ($currentPage < ceil($totalBookings / $perPage)): ?>
        <a href="/admin/bookings?page=<?= $currentPage + 1 ?>"
           class="px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
          Next
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>