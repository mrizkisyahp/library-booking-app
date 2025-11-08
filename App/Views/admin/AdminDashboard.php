<?php
/** @var array $stats */
/** @var array $recentBookings */
/** @var array $roomUsage */
use App\Core\App;
?>

<?php if ($m = App::$app->session->getFlash('success')): ?>
  <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-lg">
    <div class="flex items-center">
      <svg class="w-5 h-5 text-emerald-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
      </svg>
      <p class="text-emerald-800 font-medium"><?= htmlspecialchars($m) ?></p>
    </div>
  </div>
<?php endif; ?>

<div class="mb-8">
  <h2 class="text-3xl font-bold text-gray-900">Admin Dashboard</h2>
  <p class="text-gray-600 mt-2">Kelola sistem booking ruangan perpustakaan</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <div class="col-span-2 p-4 border-r border-r-gray-300">
    <h2 class="text-lg font-medium">
      Your Booking Statistics
    </h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-4">
      <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
        <p class="text-sm font-semibold text-gray-600 mb-2">Total Booking</p>
        <div class="text-3xl font-bold text-emerald-600">
          <?= $stats['totalBookings'] ?>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
        <p class="text-sm font-semibold text-gray-600 mb-2">Pending</p>
        <div class="text-3xl font-bold text-yellow-600">
          <?= $stats['pendingBookings'] ?>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
        <p class="text-sm font-semibold text-gray-600 mb-2">Active</p>
        <div class="text-3xl font-bold text-blue-600">
          <?= $stats['activeBookings'] ?>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
        <p class="text-sm font-semibold text-gray-600 mb-2">Completed</p>
        <div class="text-3xl font-bold text-green-600">
          <?= $stats['completedBookings'] ?>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
        <p class="text-sm font-semibold text-gray-600 mb-2">Total Rooms</p>
        <div class="text-3xl font-bold text-purple-600">
          <?= $stats['totalRooms'] ?>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
        <p class="text-sm font-semibold text-gray-600 mb-2">Available Rooms</p>
        <div class="text-3xl font-bold text-indigo-600">
          <?= $stats['availableRooms'] ?>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
        <p class="text-sm font-semibold text-gray-600 mb-2">Total Users</p>
        <div class="text-3xl font-bold text-teal-600">
          <?= $stats['totalUsers'] ?>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
        <p class="text-sm font-semibold text-gray-600 mb-2">Verified Users</p>
        <div class="text-3xl font-bold text-cyan-600">
          <?= $stats['verifiedUsers'] ?>
        </div>
      </div>
    </div>

    <div class="mt-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">
        Recent Bookings
      </h2>
      <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <?php if (empty($recentBookings)): ?>
          <p class="p-6 text-gray-600 text-center">No recent bookings.</p>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gradient-to-r from-emerald-50 to-teal-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">User</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Room</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Date & Time</th>
                  <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php foreach ($recentBookings as $booking): ?>
                  <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($booking['user_name']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($booking['room_title']) ?></td>
                    <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($booking['tanggal_penggunaan_ruang']) ?> <?= htmlspecialchars($booking['waktu_mulai']) ?> - <?= htmlspecialchars($booking['waktu_selesai']) ?></td>
                    <td class="px-6 py-4 text-sm">
                      <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $booking['status'] === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' ?>">
                        <?= htmlspecialchars(ucfirst($booking['status'])) ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="mt-8">
      <h2 class="text-xl font-semibold text-gray-900 mb-4">
        Rooms Usage
      </h2>
      <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <?php if (empty($roomUsage)): ?>
          <p class="p-6 text-gray-600 text-center">No room usage data.</p>
        <?php else: ?>
          <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-800">
              <thead class="bg-gradient-to-r from-emerald-50 to-teal-50">
                <tr>
                  <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase">Room</th>
                  <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase">Total Bookings</th>
                  <th class="px-6 py-3 text-xs font-semibold text-gray-700 uppercase">Usage (%)</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php
                $totalBookings = array_sum(array_column($roomUsage, 'booking_count'));
                foreach ($roomUsage as $usage):
                  $usagePercentage = $totalBookings > 0 ? round(($usage['booking_count'] / $totalBookings) * 100, 2) : 0;
                  ?>
                  <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"><?= htmlspecialchars($usage['nama_ruangan']) ?></td>
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

  </div>

  <div class="mt-4">
    <h2 class="text-xl font-semibold text-gray-900 mb-4">
      Quick Links
    </h2>
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
      <ul class="space-y-3">
        <li>
          <a href="/admin/bookings" class="flex items-center text-gray-700 hover:text-emerald-600 transition-colors group">
            <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span class="font-medium group-hover:underline">Manage Bookings</span>
          </a>
        </li>
        <li>
          <a href="/admin/rooms" class="flex items-center text-gray-700 hover:text-emerald-600 transition-colors group">
            <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span class="font-medium group-hover:underline">Manage Rooms</span>
          </a>
        </li>
        <li>
          <a href="/admin/users" class="flex items-center text-gray-700 hover:text-emerald-600 transition-colors group">
            <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span class="font-medium group-hover:underline">Manage Users</span>
          </a>
        </li>
        <li>
          <a href="/admin/reports" class="flex items-center text-gray-700 hover:text-emerald-600 transition-colors group">
            <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="font-medium group-hover:underline">Generate Reports</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>
