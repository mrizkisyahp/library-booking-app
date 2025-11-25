<?php
/** @var array $stats */
/** @var array $bookings */
/** @var array $pendingFeedbacks */
/** @var \App\Models\User $user */
use App\Core\Csrf;
use App\Core\App;
?>

<!-- Pending Feedback Warning -->
<?php if (!empty($pendingFeedbacks ?? [])): ?>
  <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-5 shadow-md">
    <div class="flex items-start">
      <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
      </svg>
      <div class="ml-4">
        <h3 class="font-semibold text-yellow-800 mb-2">Perhatian: Feedback Belum Diisi</h3>
        <p class="text-yellow-700 mb-3">Anda memiliki booking yang sudah selesai namun belum mengisi feedback. Harap lengkapi feedback sebelum membuat booking baru.</p>
        <ul class="space-y-2">
          <?php foreach ($pendingFeedbacks as $pending): ?>
            <li class="flex items-center justify-between bg-yellow-100 px-4 py-2 rounded-lg">
              <span class="text-yellow-800 text-sm">
                <strong><?= htmlspecialchars($pending['nama_ruangan']) ?></strong> – 
                <?= htmlspecialchars($pending['tanggal_penggunaan_ruang']) ?> 
                <?= htmlspecialchars($pending['waktu_mulai']) ?>
              </span>
              <a href="/feedback/create?booking=<?= (int)$pending['id_booking'] ?>" class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm underline">
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
<div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-3xl font-bold text-slate-800 mb-2">
        Selamat Datang, <span class="text-emerald-600"><?= htmlspecialchars($user->nama) ?></span>! 👋
      </h1>
      <p class="text-slate-600">Kelola booking ruangan Anda dengan mudah</p>
    </div>
    <div class="hidden md:block">
      <svg class="w-24 h-24 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
      </svg>
    </div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <!-- Main Content -->
  <div class="lg:col-span-2 space-y-6">
    <!-- Statistics -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
      <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
        <svg class="w-7 h-7 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        Statistik Booking Anda
      </h2>
      <?php
        $statusCards = [
          'draft' => ['label' => 'Draft', 'class' => 'from-gray-50 to-gray-100 border-gray-200'],
          'pending' => ['label' => 'Pending', 'class' => 'from-yellow-50 to-yellow-100 border-yellow-200'],
          'verified' => ['label' => 'Verified', 'class' => 'from-blue-50 to-blue-100 border-blue-200'],
          'active' => ['label' => 'Active', 'class' => 'from-emerald-50 to-emerald-100 border-emerald-200'],
          'completed' => ['label' => 'Completed', 'class' => 'from-green-50 to-green-100 border-green-200'],
          'cancelled' => ['label' => 'Cancelled', 'class' => 'from-red-50 to-red-100 border-red-200'],
          'expired' => ['label' => 'Expired', 'class' => 'from-slate-50 to-slate-100 border-slate-200'],
          'no_show' => ['label' => 'No-Show', 'class' => 'from-orange-50 to-orange-100 border-orange-200'],
        ];
      ?>
      <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border-2 border-blue-200">
          <p class="text-xs font-semibold text-blue-600 mb-1">Total</p>
          <p class="text-3xl font-bold text-blue-700"><?= $stats['totalBookings'] ?? 0 ?></p>
        </div>
        <?php foreach ($statusCards as $statusKey => $config): ?>
          <div class="bg-gradient-to-br <?= $config['class'] ?> rounded-xl p-4 border-2">
            <p class="text-xs font-semibold text-slate-600 mb-1"><?= $config['label'] ?></p>
            <p class="text-3xl font-bold text-slate-800"><?= $stats['statusCounts'][$statusKey] ?? 0 ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
      <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
        <svg class="w-7 h-7 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Booking Terbaru
      </h2>
      
      <?php if (empty($bookings)): ?>
        <div class="text-center py-12">
          <svg class="w-20 h-20 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
          <p class="text-slate-600 mb-4">Belum ada booking. Mulai booking ruangan sekarang!</p>
          <a href="/rooms" class="inline-flex items-center bg-primary text-white px-6 py-3 rounded-xl hover:bg-emerald-700 transition-all font-semibold">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Cari Ruangan
          </a>
        </div>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-linear-to-br from-emerald-600 to-emerald-800">
              <tr class="*:px-4 *:py-3 *:text-left *:text-regular *:font-semibold *:text-gray-50 *:capitalize *:tracking-wider *:whitespace-nowrap">
                <th>Ruangan</th>
                <th>Tanggal & Waktu</th>
                <th>Status</th>
                <th>Peran</th>
                <th>Kode Check-in</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <?php foreach ($bookings as $booking): ?>
              <tr class="hover:bg-gray-200 odd:bg-gray-50 even:bg-gray-100 transition-colors border-b border-gray-200">
                  <td class="px-4 py-4 font-medium text-slate-800">
                    <?= htmlspecialchars($booking['nama_ruangan'] ?? ('#' . $booking['ruangan_id'])) ?>
                  </td>
                  <td class="px-4 py-4 text-slate-600">
                    <?= htmlspecialchars($booking['tanggal_penggunaan_ruang']) ?><br>
                    <span class="text-xs text-slate-500"><?= htmlspecialchars($booking['waktu_mulai']) ?></span>
                  </td>
                  <td class="px-4 py-4">
                    <?php
                    $statusColors = [
                      'draft' => 'bg-gray-100 text-gray-800',
                      'pending' => 'bg-yellow-100 text-yellow-800',
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
                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold <?= $statusColor ?>">
                      <?= htmlspecialchars($statusLabel) ?>
                    </span>
                  </td>
                  <td class="px-4 py-4 text-slate-600">
                    <?= htmlspecialchars($booking['role']) ?>
                  </td>
                  <td class="px-4 py-4 text-center">
                    <?php if ($statusKey === 'verified' && !empty($booking['checkin_code'])): ?>
                      <span class="font-mono font-bold text-emerald-600 text-lg"><?= htmlspecialchars($booking['checkin_code']) ?></span>
                    <?php else: ?>
                      <span class="text-slate-400">—</span>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-4">
                    <?php if ($statusKey === 'draft'): ?>
                      <a href="/bookings/draft?id=<?= (int)$booking['id_booking'] ?>" class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm">
                        Lihat Draft
                      </a>
                    <?php elseif ($booking['role'] === 'PIC' && $statusKey === 'completed' && empty($booking['feedback_submitted'])): ?>
                      <a href="/feedback/create?booking=<?= (int)$booking['id_booking'] ?>" class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm">
                        Isi Feedback
                      </a>
                    <?php else: ?>
                      <span class="text-slate-400">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="mt-6 text-center">
          <a href="/my-bookings" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold">
            Lihat Semua Booking
            <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Sidebar -->
  <div class="space-y-6">
    <!-- Quick Links -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
      <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
        <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        Quick Links
      </h2>
      <div class="space-y-2">
        <a href="/rooms" class="flex items-center p-3 rounded-xl hover:bg-emerald-50 transition-colors group">
          <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <span class="font-medium text-slate-700 group-hover:text-emerald-700">Cari Ruangan</span>
        </a>
        <a href="/my-bookings" class="flex items-center p-3 rounded-xl hover:bg-emerald-50 transition-colors group">
          <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
          <span class="font-medium text-slate-700 group-hover:text-emerald-700">My Bookings</span>
        </a>  
        <a href="/profile" class="flex items-center p-3 rounded-xl hover:bg-emerald-50 transition-colors group">
          <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
          <span class="font-medium text-slate-700 group-hover:text-emerald-700">Profile</span>
        </a>
      </div>
    </div>

    <!-- Join Booking -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
      <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
        <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        </svg>
        Join Booking
      </h2>
      <p class="text-sm text-slate-600 mb-4">Punya token undangan? Gabung ke booking.</p>
      <form method="post" action="/bookings/join" class="space-y-3">
        <?= Csrf::field() ?>
        <div>
          <input type="text" name="invite_token" value="<?= htmlspecialchars($prefill ?? '') ?>"
                 class="w-full px-3 py-2 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200"
                 placeholder="Masukkan token..." required>
        </div>
        <button type="submit"
                class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-all font-semibold text-sm shadow">
          Gabung
        </button>
      </form>
    </div>
  </div>
</div>
