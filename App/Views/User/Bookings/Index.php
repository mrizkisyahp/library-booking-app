<?php

use App\Core\App;
use App\Models\Booking;
use App\Core\Csrf;
use App\Models\User;

$currentUser = App::$app->user instanceof User ? App::$app->user : null;

$roomTypes = [
  'Audio Visual',
  'Telekonferensi',
  'Kreasi dan Rekreasi',
  'Baca Kelompok',
  'Koleksi Bahasa Prancis',
  'Bimbingan & Konseling',
  'Ruang Rapat',
];

dump($bookings);

?>

<div class="min-h-screen bg-linear-to-br from-slate-50 to-slate-100">
  <div class="max-w-7xl mx-auto px-6 py-12">
    <!-- Header -->
    <div class="mb-8">
      <h2 class="text-4xl font-bold text-slate-800 mb-2">Riwayat Booking</h2>
      <p class="text-slate-600">Monitor seluruh riwayat ruangan yang pernah digunakan</p>
    </div>

    <!-- Filter Form -->

    <form method="get" action="/rooms" class="relative bg-white rounded-2xl shadow-lg p-8 mb-4">
      <?= csrf_field() ?>

      <!-- hidden checkbox peer untuk toggle panel -->
      <input type="checkbox" id="filterToggle" class="peer hidden" />

      <!-- Search input -->
      <div class="relative w-full mb-4">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none"
          stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>

        <input type="text" name="nama_ruangan" value="<?= htmlspecialchars($filters['nama_ruangan'] ?? '') ?>"
          placeholder="Cari nama ruangan..."
          class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-2xl
                      focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:outline-none transition-all" />
      </div>

      <!-- tombol filter: ini adalah label untuk #filterToggle -->
      <label for="filterToggle" class="w-full cursor-pointer flex items-center justify-center gap-3
                    py-3 px-4 border-2 border-gray-200 rounded-2xl text-gray-600
                    hover:bg-gray-100 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
          <path d="M10 5H3" />
          <path d="M12 19H3" />
          <path d="M14 3v4" />
          <path d="M16 17v4" />
          <path d="M21 12h-9" />
          <path d="M21 19h-5" />
          <path d="M21 5h-7" />
          <path d="M8 10v4" />
          <path d="M8 12H3" />
        </svg>
        <span>Filter</span>
      </label>

      <!-- PANEL FILTER (sibling dari input#filterToggle di dalam form) -->
      <div class="fixed left-0 right-0 bottom-0 h-[80vh] z-999 bg-white
               translate-y-full peer-checked:translate-y-0
               transition-transform duration-400 ease-in-out
               rounded-t-3xl shadow-xl px-8 py-8 overflow-auto">

        <div class="flex items-center justify-between mb-6">
          <h2 class="text-2xl font-bold">Filter</h2>

          <label for="filterToggle" class="cursor-pointer p-2 rounded-full hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor"
              stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
              <path d="M18 6 6 18" />
              <path d="M6 6 18 18" />
            </svg>
          </label>
        </div>

        <div class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Peminjaman</label>
            <input type="date" name="tanggal" value="<?= htmlspecialchars($filters['tanggal'] ?? '') ?>"
              class="w-full mt-1 border-2 border-gray-200 rounded-2xl py-3 px-4 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:outline-none" />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Waktu Mulai</label>
            <input type="time" name="waktu_mulai" value="<?= htmlspecialchars($filters['waktu_mulai'] ?? '') ?>"
              class="w-full mt-1 border-2 border-gray-200 rounded-2xl py-3 px-4 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:outline-none" />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Kapasitas (min)</label>
            <input type="number" name="kapasitas_min" min="0"
              value="<?= htmlspecialchars($filters['kapasitas_min'] ?? '') ?>"
              class="w-full mt-1 border-2 border-gray-200 rounded-2xl py-3 px-4 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:outline-none" />
          </div>

          <div>
            <p class="block text-sm font-medium text-slate-700 mb-2">Jenis Ruangan</p>
            <div class="flex flex-wrap gap-2">
              <?php foreach ($roomTypes as $roomType):
                $safeId = 'rt_' . preg_replace('/[^a-z0-9]+/i', '_', strtolower($roomType));
                ?>
                <label for="<?= $safeId ?>"
                  class="flex items-center bg-slate-100 py-2 px-4 border-2 border-gray-300 rounded-2xl cursor-pointer checked:bg-emerald-600 checked:text-white">
                  <input id="<?= $safeId ?>" type="checkbox" name="jenis_ruangan[]"
                    value="<?= htmlspecialchars($roomType) ?>" class="appearance-none " <?= in_array($roomType, $filters['jenis_ruangan'] ?? []) ? 'checked' : '' ?> />
                  <span class="text-sm"><?= htmlspecialchars($roomType) ?></span>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="flex gap-3">
            <button type="submit"
              class="flex-1 px-4 py-3 bg-emerald-600 text-white rounded-2xl font-semibold hover:bg-emerald-700 transition">
              Terapkan
            </button>

            <a href="/rooms"
              class="flex-1 px-4 py-3 border-2 border-slate-300 rounded-2xl text-slate-700 text-center hover:bg-slate-100 transition">
              Reset
            </a>
          </div>
        </div>
      </div>
    </form>

    <?php
    foreach ($bookings as $booking):
      ?>

      <div class="rounded-3xl border-2 border-gray-400 bg-gray-100 mb-4">
        <div class="flex flex-col justify-start p-6">
          <p class="font-bold text-2xl mb-2">
            <?= htmlspecialchars($booking->nama_ruangan) ?>
          </p>
          <p class="mb-2">
            <?= htmlspecialchars($booking->jenis_ruangan) ?>
          </p>
          <div class="w-full">
            <?php
            $statusColors = [
              'draft' => 'bg-gray-300 text-gray-800 border-gray-400',
              'pending' => 'bg-yellow-300 text-yellow-800 border-yellow-400',
              'verified' => 'bg-blue-100 text-blue-800 border-blue-400',
              'active' => 'bg-emerald-100 text-emerald-800 border-emerald-400',
              'completed' => 'bg-green-100 text-green-800 border-green-400',
              'cancelled' => 'bg-red-100 text-red-800 border-red-400',
              'expired' => 'bg-slate-100 text-slate-700 border-slate-400',
              'no_show' => 'bg-orange-100 text-orange-800 border-orange-400',
            ];
            $statusKey = strtolower($booking->status);
            $statusColor = $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-800';
            $statusLabel = ucwords(str_replace('_', ' ', $statusKey));
            ?>
            <div class="px-4 py-2 mb-4 rounded-3xl font-regular border text-sm <?= $statusColor ?>">
              Status:
              <?= htmlspecialchars($statusLabel) ?>
              <!-- 🤡🤡🤡 -->
              <?php if ($booking->status === 'draft'): ?>
                (Menunggu Anggota)
              <?php elseif ($booking->status === 'pending'): ?>
                (Menunggu Konfirmasi)
              <?php elseif ($booking->status === 'verified'): ?>
                (Terkonfirmasi)
              <?php elseif ($booking->status === 'active'): ?>
                (Sedang berlangsung)
              <?php elseif ($booking->status === 'completed'): ?>
                (Selesai)
              <?php endif ?>
            </div>

            <p class="mb-4 flex gap-2 items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-calendar-days-icon lucide-calendar-days size-4">
                <path d="M8 2v4" />
                <path d="M16 2v4" />
                <rect width="18" height="18" x="3" y="4" rx="2" />
                <path d="M3 10h18" />
                <path d="M8 14h.01" />
                <path d="M12 14h.01" />
                <path d="M16 14h.01" />
                <path d="M8 18h.01" />
                <path d="M12 18h.01" />
                <path d="M16 18h.01" />
              </svg>
              <?= htmlspecialchars(formatTanggal($booking->tanggal_penggunaan_ruang)) ?>
            </p>

            <p class="mb-4 flex gap-2 items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-clock3-icon lucide-clock-3 size-4">
                <path d="M12 6v6h4" />
                <circle cx="12" cy="12" r="10" />
              </svg>
              <?= htmlspecialchars(formatWaktu($booking->waktu_mulai)) ?>
            </p>

            <p class="mb-4 flex gap-2 items-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-users-round-icon lucide-users-round size-4">
                <path d="M18 21a8 8 0 0 0-16 0" />
                <circle cx="10" cy="8" r="5" />
                <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3" />
              </svg>

              <?= $booking->kapasitas_min ?> / <?= $booking->kapasitas_max ?> peserta
            </p>

            <div class="w-full">
              <?php if ($booking->status === 'draft'): ?>
                <a href="/bookings/draft?id=<?= (int) $booking->id_booking ?>"
                  class="inline-block bg-emerald-600 hover:bg-emerald-700 font-regular text-sm text-white w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                  Lihat Detail
                </a>
              <?php else: ?>
                <a href="/bookings/detail?id=<?= (int) $booking->id_booking ?>"
                  class="inline-block bg-emerald-600 hover:bg-emerald-700 font-regular text-sm text-white w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                  Lihat Detail
                </a>
              <?php endif; ?>
              <?php if ($booking->status === 'completed' && empty($booking->feedback_submitted)): ?>
                <a href="/feedback/create?booking=<?= (int) $booking->id_booking ?>"
                  class="inline-block text-emerald-600 hover:text-emerald-700 font-regular text-sm active:text-emerald-800 w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide underline focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                  Isi Feedback
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

  </div>
</div>
