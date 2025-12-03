<?php

use App\Core\App;
use App\Models\Booking;
use App\Core\Csrf;
use App\Models\User;

$currentUser = App::$app->user instanceof User ? App::$app->user : null;

?>

<!-- echo '<pre>';
print_r($bookings);
echo '</pre>'; -->

<div class="min-h-screen bg-linear-to-br from-slate-50 to-slate-100">
  <div class="max-w-7xl mx-auto px-6 py-12">
    <!-- Header -->
    <div class="mb-8">
      <h2 class="text-4xl font-bold text-slate-800 mb-2">Riwayat Booking</h2>
      <p class="text-slate-600">Monitor seluruh riwayat ruangan yang pernah digunakan</p>
    </div>

    <!-- Filter Form -->
    <form method="get" action="/rooms" class="bg-white rounded-2xl shadow-lg p-8 mb-10">
      <!-- Keyword Search -->
      <div class="mb-6">
        <label class="block text-sm font-semibold text-slate-700 mb-3">
          <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          Cari Nama Ruangan
        </label>
        <input type="text" name="nama_ruangan" value="<?= htmlspecialchars($filters['nama_ruangan'] ?? '') ?>"
          placeholder="Cari berdasarkan nama ruangan..."
          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
      </div>
    </form>
    <!-- Booking List -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
      <?php if (empty($bookings['mybooking'])): ?>
        <div class="p-8 text-center text-slate-500">
          Belum ada riwayat booking.
        </div>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Ruangan</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal &
                  Waktu</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <?php foreach ($bookings['mybooking'] as $booking): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                  <td class="px-6 py-4">
                    <div class="font-semibold text-slate-800">
                      <?= htmlspecialchars($booking->nama_ruangan ?? 'Ruangan #' . $booking->ruangan_id) ?>
                    </div>
                    <div class="text-xs text-slate-500">
                      ID: #<?= $booking->id_booking ?>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="text-sm text-slate-800">
                      <?= date('d M Y', strtotime($booking->tanggal_penggunaan_ruang)) ?>
                    </div>
                    <div class="text-xs text-slate-500">
                      <?= substr($booking->waktu_mulai, 0, 5) ?> - <?= substr($booking->waktu_selesai, 0, 5) ?>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <?php
                    $statusColors = [
                      'pending' => 'bg-yellow-100 text-yellow-800',
                      'verified' => 'bg-blue-100 text-blue-800',
                      'active' => 'bg-emerald-100 text-emerald-800',
                      'completed' => 'bg-gray-100 text-gray-800',
                      'cancelled' => 'bg-rose-100 text-rose-800',
                      'draft' => 'bg-slate-100 text-slate-800',
                    ];
                    $statusClass = $statusColors[$booking->status] ?? 'bg-slate-100 text-slate-800';
                    ?>
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold <?= $statusClass ?>">
                      <?= ucfirst($booking->status) ?>
                    </span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <a href="/bookings/detail?id=<?= $booking->id_booking ?>"
                      class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 hover:border-slate-300 transition-all">
                      Detail
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- Pagination could go here -->

  </div>
</div>