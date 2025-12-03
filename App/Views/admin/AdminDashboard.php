<div class="mb-8">
  <h2 class="text-3xl font-bold text-gray-900">Admin Dashboard</h2>
  <p class="text-gray-600 mt-2">Kelola sistem booking ruangan perpustakaan</p>

</div>

<?php
$bookingStats = $stats['bookingStats'];
$resourceStats = $stats['resources'];
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
$rooms = $resourceStats['rooms'];
$users = $resourceStats['users'];
?>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
  <div class="xl:col-span-2 space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="text-xl font-semibold text-gray-900">Booking Stats</h3>
          <p class="text-sm text-gray-500">Total booking dan distribusi status terkini</p>
        </div>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
        <div class="bg-linear-to-br from-blue-50 to-blue-100 rounded-xl p-4 border-2 border-blue-200">
          <p class="text-xs font-semibold text-blue-600 mb-1">Total Booking</p>
          <p class="text-3xl font-bold text-blue-800"><?= $bookingStats['total'] ?? 0 ?></p>
        </div>
        <?php foreach ($statusCards as $key => $config): ?>
          <div class="bg-linear-to-br <?= $config['class'] ?> rounded-xl p-4 border-2">
            <p class="text-xs font-semibold text-slate-600 mb-1"><?= $config['label'] ?></p>
            <p class="text-3xl font-bold text-slate-900"><?= $bookingStats['statuses'][$key] ?? 0 ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="text-xl font-semibold text-gray-900">System / Resources</h3>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <h4 class="text-sm font-semibold text-gray-600 mb-3">Rooms</h4>
          <div class="grid grid-cols-3 gap-3">
            <div class="bg-linear-to-br from-purple-50 to-purple-100 rounded-xl p-4 border-2 border-purple-200">
              <p class="text-xs font-semibold text-purple-600 mb-1">Total</p>
              <p class="text-2xl font-bold text-purple-800"><?= $rooms['total'] ?></p>
            </div>
            <div class="bg-linear-to-br from-emerald-50 to-emerald-100 rounded-xl p-4 border-2 border-emerald-200">
              <p class="text-xs font-semibold text-emerald-600 mb-1">Available</p>
              <p class="text-2xl font-bold text-emerald-800"><?= $rooms['available'] ?></p>
            </div>
            <div class="bg-linear-to-br from-rose-50 to-rose-100 rounded-xl p-4 border-2 border-rose-200">
              <p class="text-xs font-semibold text-rose-600 mb-1">Unavailable</p>
              <p class="text-2xl font-bold text-rose-800"><?= $rooms['unavailable'] ?></p>
            </div>
          </div>
        </div>
        <div>
          <h4 class="text-sm font-semibold text-gray-600 mb-3">Users</h4>
          <div class="grid grid-cols-3 gap-3">
            <div class="bg-linear-to-br from-teal-50 to-teal-100 rounded-xl p-4 border-2 border-teal-200">
              <p class="text-xs font-semibold text-teal-600 mb-1">Total</p>
              <p class="text-2xl font-bold text-teal-800"><?= $users['total'] ?></p>
            </div>
            <div class="bg-linear-to-br from-cyan-50 to-cyan-100 rounded-xl p-4 border-2 border-cyan-200">
              <p class="text-xs font-semibold text-cyan-600 mb-1">Active</p>
              <p class="text-2xl font-bold text-cyan-800"><?= $users['active'] ?></p>
            </div>
            <div class="bg-linear-to-br from-amber-50 to-amber-100 rounded-xl p-4 border-2 border-amber-200">
              <p class="text-xs font-semibold text-amber-600 mb-1">Pending Kubaca</p>
              <p class="text-2xl font-bold text-amber-800"><?= $users['pending kubaca'] ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">
        Recent Bookings
      </h2>
      <?php if (empty($recentBookings)): ?>
        <p class="p-6 text-gray-600 text-center">No recent bookings.</p>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-linear-to-r from-emerald-50 to-teal-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Room</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date & Time</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Feedback</th>
                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php
              $statusBadges = [
                'draft' => 'bg-gray-100 text-gray-800',
                'pending' => 'bg-yellow-100 text-yellow-800',
                'verified' => 'bg-blue-100 text-blue-800',
                'active' => 'bg-emerald-100 text-emerald-800',
                'completed' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-red-100 text-red-800',
                'expired' => 'bg-slate-100 text-slate-600',
                'no_show' => 'bg-orange-100 text-orange-800',
              ];
              ?>
              <?php foreach ($recentBookings as $booking): ?>
                <?php
                $statusKey = strtolower($booking->status);
                $badgeClass = $statusBadges[$statusKey] ?? 'bg-gray-100 text-gray-800';
                $statusLabel = ucwords(str_replace('_', ' ', $statusKey));
                ?>
                <tr class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 text-sm text-gray-900 capitalize"><?= htmlspecialchars($booking->nama) ?></td>
                  <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($booking->nama_ruangan) ?></td>
                  <td class="px-6 py-4 text-sm text-gray-700">
                    <?= htmlspecialchars($booking->tanggal_penggunaan_ruang) ?>
                    <?= htmlspecialchars($booking->waktu_mulai) ?> - <?= htmlspecialchars($booking->waktu_selesai) ?>
                  </td>
                  <td class="px-6 py-4 text-sm">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $badgeClass ?>">
                      <?= htmlspecialchars($statusLabel) ?>
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm">
                    <?php if (!empty($booking->id_feedback)): ?>
                      <a href="/admin/feedback/detail?id=<?= (int) $booking->id_feedback ?>"
                        class="text-emerald-600 hover:text-emerald-700 font-semibold text-sm">
                        Lihat Feedback
                      </a>
                    <?php else: ?>
                      <span class="text-gray-400 text-sm">Tidak ada</span>
                    <?php endif; ?>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm space-y-2">
                    <a href="/admin/bookings/detail?id=<?= (int) $booking->id_booking ?>"
                      class="inline-flex items-center px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-700 hover:bg-slate-50 transition-colors">
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

    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">
        Rooms Usage
      </h2>
      <?php if (empty($roomUsage)): ?>
        <p class="p-6 text-gray-600 text-center">No room usage data.</p>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left text-gray-800">
            <thead class="bg-linear-to-r from-emerald-50 to-teal-50">
              <tr>
                <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase">Room</th>
                <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase">Total Bookings</th>
                <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase">Usage (%)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php
              $totalBookingsRoom = array_sum(array_map(fn($usage) => $usage['booking_count'], $roomUsage));
              foreach ($roomUsage as $usage):
                $usagePercentage = $totalBookingsRoom > 0 ? round(($usage['booking_count'] / $totalBookingsRoom) * 100, 2) : 0;
                ?>
                <tr class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                    <?= htmlspecialchars($usage['nama_ruangan']) ?>
                  </td>
                  <td class="px-6 py-4 text-gray-700"><?= $usage['booking_count'] ?></td>
                  <td class="px-6 py-4 text-gray-900">
                    <div class="flex items-center">
                      <span class="font-semibold"><?= $usagePercentage ?>%</span>
                      <div class="ml-3 w-24 bg-gray-200 rounded-full h-2">
                        <div class="bg-emerald-600 h-2 rounded-full" style="width: <?= $usagePercentage ?>%"></div>
                      </div>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">
        Quick Links
      </h2>
      <ul class="space-y-3">
        <li>
          <a href="/admin/bookings"
            class="flex items-center text-gray-700 hover:text-emerald-600 transition-colors group">
            <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span class="font-medium group-hover:underline">Manage Bookings</span>
          </a>
        </li>
        <li>
          <a href="/admin/rooms" class="flex items-center text-gray-700 hover:text-emerald-600 transition-colors group">
            <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span class="font-medium group-hover:underline">Manage Rooms</span>
          </a>
        </li>
        <li>
          <a href="/admin/users" class="flex items-center text-gray-700 hover:text-emerald-600 transition-colors group">
            <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="font-medium group-hover:underline">Manage Users</span>
          </a>
        </li>
        <li>
          <a href="/admin/reports"
            class="flex items-center text-gray-700 hover:text-emerald-600 transition-colors group">
            <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="font-medium group-hover:underline">Generate Reports</span>
          </a>
        </li>
      </ul>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6">
      <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
        <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        </svg>
        Join Booking
      </h2>
      <p class="text-sm text-slate-600 mb-4">Punya token undangan? Gabung ke booking.</p>
      <form method="post" action="/bookings/join" class="space-y-3">
        <?= csrf_field() ?>
        <div>
          <input type="text" name="invite_token" value="<?= htmlspecialchars($prefill ?? '') ?>"
            class="w-full px-3 py-2 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200"
            placeholder="Masukkan code..." required>
        </div>
        <button type="submit"
          class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-all font-semibold text-sm shadow cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
          Gabung
        </button>
      </form>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6">
      <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="lucide lucide-message-square-icon lucide-message-square mr-2 text-emerald-600">
          <path
            d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z" />
        </svg> Feedback Booking
      </h2>
      <p class="text-sm text-slate-600 mb-4">Monitor pengalaman pengguna setelah meminjam ruangan.</p>
      <div class="space-y-3">
        <a href="/admin/feedback"
          class="block text-center w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-emerald-700 transition-all font-semibold text-sm shadow capitalize cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
          Lihat feedback
        </a>
      </div>
    </div>
  </div>