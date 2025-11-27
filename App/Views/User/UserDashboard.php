<?php
/** @var array $stats */
/** @var array $bookings */
/** @var array $pendingFeedbacks */
/** @var \App\Models\User $user */
use App\Core\Csrf;
use Carbon\Carbon;
use App\Core\App;
Carbon::setLocale('id');

function formatWaktu($waktu)
{
  return Carbon::parse($waktu)->format('H:i') . ' WIB';
}

function formatTanggal($tanggal)
{
  return Carbon::parse($tanggal)->translatedFormat('l, d F Y');
}
?>

<!-- Pending Feedback Warning -->
<?php if (!empty($pendingFeedbacks ?? [])): ?>
  <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-5 shadow-md">
    <div class="flex items-start">
      <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
      <div class="ml-4">
        <h3 class="font-semibold text-yellow-800 mb-2">Perhatian: Feedback Belum Diisi</h3>
        <p class="text-yellow-700 mb-3">Anda memiliki booking yang sudah selesai namun belum mengisi feedback. Harap
          lengkapi feedback sebelum membuat booking baru.</p>
        <ul class="space-y-2">
          <?php foreach ($pendingFeedbacks as $pending): ?>
            <li class="flex items-center justify-between bg-yellow-100 px-4 py-2 rounded-lg">
              <span class="text-yellow-800 text-sm">
                <strong><?= htmlspecialchars($pending['nama_ruangan']) ?></strong> –
                <?= htmlspecialchars($pending['tanggal_penggunaan_ruang']) ?>
                <?= htmlspecialchars($pending['waktu_mulai']) ?>
              </span>
              <a href="/feedback/create?booking=<?= (int) $pending['id_booking'] ?>"
                class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm underline">
                Isi Feedback
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
<?php endif; ?>

<!-- Welcome Header -->
<div class="rounded-2xl p-4 mb-6">
  <div class="flex items-center justify-between mt-6 px-6">
    <div>
      <h1 class="text-4xl font-bold text-white md:text-black mb-2">
        Selamat Datang, <span
          class="text-emerald-100 md:text-emerald-800 capitalize"><?= htmlspecialchars($user->nama) ?></span>! 👋
      </h1>
      <p class="text-white md:text-black">Kelola booking ruangan Anda dengan mudah</p>
    </div>
    <div class="hidden md:block">
      <!-- <svg class="w-24 h-24 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
      </svg> -->
    </div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <!-- Main Content -->
  <div class="lg:col-span-2 space-y-6">
    <!-- Statistics -->
    <div class="bg-gray-100 rounded-t-3xl md:rounded-3xl shadow-lg p-6 md:p-0">
      <div class="bg-white rounded-3xl p-8">
        <h2 class="text-4xl font-bold text-slate-800 mb-6 flex items-center">
          <svg class="size-9 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          Booking Saya
        </h2>

        <?php if (empty($bookings)): ?>
          <div class="text-center py-12 rounded-3xl border-2 border-gray-400 bg-gray-100 mb-4">
            <div class="flex justify-center mb-6 text-gray-600">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-frown-icon lucide-frown size-9">
                <circle cx="12" cy="12" r="10" />
                <path d="M16 16s-1.5-2-4-2-4 2-4 2" />
                <line x1="9" x2="9.01" y1="9" y2="9" />
                <line x1="15" x2="15.01" y1="9" y2="9" />
              </svg>
            </div>
            <p class="text-slate-600 mb-4">Sepertinya tidak ada booking yang aktif </p>
          </div>

          <a href="/rooms"
            class="inline-flex w-full justify-center items-center bg-primary text-white px-6 py-4 rounded-2xl hover:bg-emerald-700 active:bg-emerald-700 transition-all font-regular mb-4 shadow-md cursor-pointer focus:outline-none focus:ring-2 focus:bg-emerald-600 focus:ring-emerald-500 focus:ring-offset-2">
            <!-- <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg> -->
            Cari Ruangan
          </a>

          <a href="/"
            class="inline-flex w-full justify-center items-center bg-primary text-white px-6 py-4 rounded-2xl hover:bg-emerald-700 active:bg-emerald-700 transition-all font-regular shadow-md cursor-pointer focus:outline-none focus:ring-2 focus:bg-emerald-600 focus:ring-emerald-500 focus:ring-offset-2">
            Masukkan Kode Undangan
          </a>
        <?php else: ?>
          <?php foreach ($bookings as $booking): ?>
            <div class="rounded-3xl border-2 border-gray-400 bg-gray-100 mb-4">
              <div class="flex flex-col justify-start p-6">
                <p class="font-bold text-2xl mb-4">
                  <?= htmlspecialchars($booking['nama_ruangan'] ?? ('#' . $booking['ruangan_id'])) ?>
                </p>
                <div class="w-full">
                  <?php
                  $statusColors = [
                    'draft' => 'bg-gray-300 text-gray-800',
                    'pending' => 'bg-yellow-300 text-yellow-800',
                    'verified' => 'bg-blue-100 text-blue-800',
                    'active' => 'bg-emerald-100 text-emerald-800',
                    'completed' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                    'expired' => 'bg-slate-100 text-slate-700',
                    'no_show' => 'bg-orange-100 text-orange-800',
                  ];
                  $statusKey = strtolower($booking['status']);
                  $statusColor = $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-800';
                  $statusLabel = ucwords(str_replace('_', ' ', $statusKey));
                  ?>
                  <div class="px-4 py-2 mb-4 rounded-4xl font-regular tracking-wide <?= $statusColor ?>">
                    Status:
                    <?= htmlspecialchars($statusLabel) ?>
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
                    <?= htmlspecialchars(formatTanggal($booking['tanggal_penggunaan_ruang'])) ?>
                  </p>

                  <p class="mb-4 flex gap-2 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="lucide lucide-clock3-icon lucide-clock-3 size-4">
                      <path d="M12 6v6h4" />
                      <circle cx="12" cy="12" r="10" />
                    </svg>
                    <?= htmlspecialchars(formatWaktu($booking['waktu_mulai'])) ?>
                  </p>

                  <p class="mb-4 flex gap-2 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="lucide lucide-users-round-icon lucide-users-round size-4">
                      <path d="M18 21a8 8 0 0 0-16 0" />
                      <circle cx="10" cy="8" r="5" />
                      <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3" />
                    </svg>
                    <!-- <?= (int) $currentMembers ?> /
                    <?= isset($maximumMembers) && $maximumMembers > 0 ? (int) $maximumMembers : '∞' ?> peserta
                    <?php if (isset($requiredMembers) && $requiredMembers > 0): ?>
                      · Min <?= (int) $requiredMembers ?>
                    <?php endif; ?> -->
                    1/6 peserta . Min 1
                  </p>

                  <div class="w-full">
                    <?php if ($statusKey === 'draft'): ?>
                      <a href="/bookings/draft?id=<?= (int) $booking['id_booking'] ?>"
                        class="inline-block bg-emerald-600 hover:bg-emerald-700 font-semibold text-sm text-white w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                        Lihat Draft
                      </a>
                    <?php elseif ($booking['role'] === 'PIC' && $statusKey === 'completed' && empty($booking['feedback_submitted'])): ?>
                      <a href="/feedback/create?booking=<?= (int) $booking['id_booking'] ?>"
                        class="inline-block text-emerald-600 hover:text-emerald-700 font-semibold text-sm active:text-emerald-800 w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide underline focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                        Isi Feedback
                      </a>
                    <?php else: ?>
                      <span class="inline-block text-slate-400 w-full px-4 py-2 rounded-xl text-center">—</span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif ?>

      </div>
    </div>

  </div>
  <div>

  </div>

</div>