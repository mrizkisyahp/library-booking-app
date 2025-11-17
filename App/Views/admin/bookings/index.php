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
          <thead class="bg-linear-to-r from-emerald-50 to-teal-50">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">User ID</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Ruangan ID</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal & Waktu
              </th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Feedback</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php foreach ($bookings as $booking): ?>
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  #<?= htmlspecialchars($booking['user_id']) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  #<?= htmlspecialchars($booking['ruangan_id']) ?>
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  <div class="font-medium"><?= htmlspecialchars($booking['tanggal_penggunaan_ruang']) ?></div>
                  <div class="text-gray-500"><?= htmlspecialchars($booking['waktu_mulai']) ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusColors[$booking['status']] ?? 'bg-gray-100 text-gray-800' ?>">
                    <?= htmlspecialchars(ucfirst($booking['status'])) ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-sm">
                  <?php if (!empty($booking['feedback_id'])): ?>
                    <a href="/admin/feedback/detail?id=<?= (int) $booking['feedback_id'] ?>"
                      class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm">Lihat Feedback</a>
                  <?php else: ?>
                    <span class="text-gray-400 text-sm">Tidak ada</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-y-2">
                  <a href="/admin/bookings/detail?id=<?= (int) $booking['id_booking'] ?>"
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