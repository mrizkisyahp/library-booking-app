<?php

$statusColors = [
  'pending' => 'bg-yellow-100 text-yellow-800 border border-yellow-300',
  'verified' => 'bg-blue-100 text-blue-800 border border-blue-300',
  'active' => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
  'completed' => 'bg-green-100 text-green-800 border border-green-300',
  'cancelled' => 'bg-red-100 text-red-800 border border-red-300',
  'expired' => 'bg-gray-100 text-gray-600 border-gray-300',
  'no_show' => 'bg-orange-100 text-orange-800 border border-orange-300'
];

$statusOptions = [
  'pending' => 'Pending',
  'verified' => 'Verified',
  'active' => 'Active',
  'completed' => 'Completed',
  'cancelled' => 'Cancelled',
  'expired' => 'Expired',
  'no_show' => 'No Show'
];
?>

<div class="p-6">
  <!-- Header -->
  <div class="mb-8 flex flex-col md:flex-row justify-between items-center">
    <div>
      <h2 class="text-3xl font-bold text-gray-900 mb-2">Manajemen Booking</h2>
      <p class="text-gray-600">Kelola dan verifikasi booking ruangan</p>
    </div>
    <div class="flex gap-4 mt-4 md:mt-0">
      <a href="/admin/blocked-dates"
        class="flex gap-2 bg-red-500 shadow text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
        Blocked Dates
      </a>
      <a href="/admin/bookings/create"
        class="flex gap-2 bg-primary shadow text-white px-8 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="lucide lucide-plus-icon lucide-plus">
          <path d="M5 12h14" />
          <path d="M12 5v14" />
        </svg>
        Tambah Booking
      </a>
    </div>
  </div>

  <section class="bg-white shadow rounded-lg p-6 mb-8 border border-gray-100">
    <h2 class="text-lg font-semibold flex items-center gap-2 mb-4">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        class="lucide lucide-sliders-horizontal-icon lucide-sliders-horizontal">
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
      Pencarian & Filter
    </h2>
    <hr class="h-1 mx-auto py-2 w-full text-gray-400">
    <form method="get" action="/admin/bookings"
      class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 px-4 md:px-8">
      <div class="flex flex-col md:flex-row items-start md:items-center gap-4 md:gap-8">
        <label class="flex flex-col md:flex-row items-center gap-4 capitalize">
          <div class="items-center gap-4 hidden md:flex w-full">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-search-icon lucide-search size-5">
              <path d="m21 21-4.34-4.34" />
              <circle cx="11" cy="11" r="8" />
            </svg>
            <span class="text-sm">Kata kunci</span>
          </div>
          <input type="text" name="keyword"
            class="w-full flex grow border border-gray-300 rounded-lg px-4 py-1 text-sm accent-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
            value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>" placeholder="Kata Kunci">
        </label>
        <label class="flex items-center gap-4">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-chart-column-decreasing-icon lucide-chart-column-decreasing size-5">
            <path d="M13 17V9" />
            <path d="M18 17v-3" />
            <path d="M3 3v16a2 2 0 0 0 2 2h16" />
            <path d="M8 17V5" />
          </svg>
          Status:
          <div class="relative">
            <button id="dropdownButton" type="button" data-dropdown-toggle="dropdown"
              class="flex items-center justify-center gap-4 bg-primary text-white box-border border border-transparent shadow font-medium leading-5 px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition">
              <span class="capitalize"><?= htmlspecialchars($filters['status'] ?: 'Semua') ?></span>
              <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-chevron-down-icon lucide-chevron-down mt-0.5">
                <path d="m6 9 6 6 6-6" />
              </svg>
            </button>
            <div id="dropdown"
              class="absolute mt-2 origin-top transition-all duration-150 ease-out scale-95 opacity-0 z-10 hidden bg-gray-100 border border-gray-200 rounded shadow">
              <ul class="p-2 text-sm font-medium space-y-1" aria-labelledby="dropdownButton">
                <?php foreach ($statusOptions as $value => $label): ?>
                  <li class="px-4 py-2 rounded cursor-pointer hover:bg-gray-200 transition
                    <?php if (($filters['status'] ?? '') === $value): ?> bg-gray-300 <?php endif; ?>"
                    data-value="<?= htmlspecialchars($value) ?>">
                    <?= htmlspecialchars($label) ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
            <input type="hidden" id="statusSelected" name="status" value="<?= $filters['status'] ?? '' ?>">
            <input type="hidden" name="view" value="<?= htmlspecialchars($activeView ?? 'today') ?>">
          </div>
        </label>
      </div>
      <div class="flex items-center gap-8 text-sm">
        <button type="submit"
          class="cursor-pointer px-4 py-2 border-2 border-emerald-600 rounded-lg font-medium text-emerald-50 bg-emerald-600 hover:bg-emerald-700 tracking-wider transition-all focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
          Terapkan
        </button>
        <a href="/admin/bookings"
          class="cursor-pointer px-4 py-2 border-2 border-zinc-300 rounded-lg font-medium text-zinc-700 hover:bg-zinc-50 transition-all tracking-wider focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2">
          Bersihkan
        </a>
      </div>
    </form>
  </section>


  <!-- Booking Management Tabs & Status Filters Combined -->
  <section class="bg-white shadow rounded-lg border border-gray-100 mb-6">
    <!-- Main Tabs: Today / All -->
    <div class="flex gap-6 px-6 pt-4 border-b border-gray-200">
      <?php
      $totalCount = array_sum($statusCounts);
      $pendingCount = $statusCounts['pending'] ?? 0;
      ?>
      <button onclick="location.href='/admin/bookings?view=today'"
        class="px-4 py-3 font-semibold transition-all relative <?= $activeView === 'today' ? 'text-emerald-600' : 'text-gray-600 hover:text-gray-800' ?>">
        Booking Today
        <?php if ($activeView === 'today'): ?>
          <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600"></div>
        <?php endif; ?>
      </button>
      <button onclick="location.href='/admin/bookings?view=all'"
        class="px-4 py-3 font-semibold transition-all relative <?= $activeView === 'all' ? 'text-emerald-600' : 'text-gray-600 hover:text-gray-800' ?>">
        All Booking
        <?php if ($activeView === 'all'): ?>
          <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600"></div>
        <?php endif; ?>
      </button>
    </div>

    <!-- Status Filters -->
    <div class="flex gap-6 px-6 py-3 bg-gray-50 border-b border-gray-200">
      <?php
      $statusFilters = [
        'pending' => ['label' => 'Pending', 'count' => $statusCounts['pending'] ?? 0],
        'verified' => ['label' => 'Verified', 'count' => $statusCounts['verified'] ?? 0],
        'active' => ['label' => 'Active', 'count' => $statusCounts['active'] ?? 0],
        'completed' => ['label' => 'Completed', 'count' => $statusCounts['completed'] ?? 0],
        'cancelled' => ['label' => 'Cancelled', 'count' => $statusCounts['cancelled'] ?? 0],
        'expired' => ['label' => 'Expired', 'count' => $statusCounts['expired'] ?? 0],
        'no_show' => ['label' => 'No Show', 'count' => $statusCounts['no_show'] ?? 0],
      ];

      foreach ($statusFilters as $statusKey => $config):
        $isActive = ($activeStatus === $statusKey);
        ?>
        <a href="/admin/bookings?status=<?= $statusKey ?>&view=<?= htmlspecialchars($activeView) ?>"
          class="relative px-2 py-2 font-medium transition-all <?= $isActive ? 'text-gray-900' : 'text-gray-600 hover:text-gray-800' ?>">
          <?= $config['label'] ?> (<?= $config['count'] ?>)
          <?php if ($isActive): ?>
            <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gray-900"></div>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </section>



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
    <div class="bg-white rounded-md shadow-md overflow-x-auto border border-gray-200">
      <table class="w-full text-sm text-left">
        <thead class="bg-linear-to-br from-emerald-600 to-emerald-800">
          <tr
            class=" *:px-6 *:py-3  *:text-left *:text-regular *:font-semibold *:text-gray-50 *:capitalize *:tracking-wider *:whitespace-nowrap">
            <th scope="col">User</th>
            <th scope="col">Ruangan</th>
            <th scope="col">Tanggal & Waktu</th>
            <th scope="col">Status</th>
            <th scope="col">Feedback</th>
            <th scope="col">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php foreach ($bookings as $booking): ?>
            <tr class="hover:bg-gray-200 odd:bg-gray-50 even:bg-gray-100 transition-colors border-b border-gray-200">
              <th scope="row" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 capitalize">
                <?= htmlspecialchars($booking->nama) ?>
              </th>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                <?= htmlspecialchars($booking->nama_ruangan) ?>
              </td>
              <td class="px-6 py-4 text-sm text-gray-700">
                <div class="font-medium"><?= htmlspecialchars($booking->tanggal_penggunaan_ruang) ?></div>
                <div class="text-gray-500"><?= htmlspecialchars($booking->waktu_mulai) ?> -
                  <?= htmlspecialchars($booking->waktu_selesai) ?>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow <?= $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800' ?>">
                  <?= htmlspecialchars(ucfirst($booking->status)) ?>
                </span>
                <?php if (!empty($booking->has_pending_reschedule)): ?>
                  <span
                    class="block mt-1 px-2 py-0.5 text-xs font-medium text-orange-700 bg-orange-100 rounded-full border border-orange-300">
                    ⏳ Request Reschedule
                  </span>
                <?php elseif (!empty($booking->has_been_rescheduled)): ?>
                  <span class="block mt-1 px-2 py-0.5 text-xs font-medium text-amber-700 bg-amber-100 rounded-full">
                    🔄 Rescheduled
                  </span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-sm">
                <?php if (!empty($booking->id_feedback)): ?>
                  <a href="/admin/feedback/detail?id=<?= (int) $booking->id_feedback ?>"
                    class="text-emerald-600 hover:text-emerald-700 hover:underline font-semibold text-sm flex gap-2 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="lucide lucide-message-square-icon lucide-message-square size-4">
                      <path
                        d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z" />
                    </svg>
                    Lihat Feedback
                  </a>
                <?php else: ?>
                  <span class="text-gray-400 text-sm">Tidak ada</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm space-y-2 flex gap-2">
                <a href="/admin/bookings/detail?id=<?= (int) $booking->id_booking ?>"
                  class="items-center px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-700 hover:bg-slate-50 transition-colors flex gap-2">
                  Detail
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-arrow-right-icon lucide-arrow-right size-4">
                    <path d="M5 12h14" />
                    <path d="m12 5 7 7-7 7" />
                  </svg>
                </a>
                <a href="/admin/bookings/edit?id=<?= (int) $booking->id_booking ?>"
                  class="inline-flex items-center px-4 py-2 bg-orange-200 rounded-lg font-medium text-orange-900 hover:bg-orange-400 transition-colors cursor-pointer border-2 border-orange-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 text-sm">
                  Edit
                </a>
                <form action="/admin/bookings/delete" method="POST" class="inline">
                  <?= csrf_field() ?>
                  <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
                  <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-red-200 rounded-lg font-medium text-red-900 hover:bg-red-400 transition-colors cursor-pointer border-2 border-red-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 text-sm">
                    Hapus
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- Pagination -->
<?php if ($pagination->total > 0): ?>
  <?php
  $paginationQuery = array_filter($filters, fn($value) => $value !== '' && $value !== []);
  $paginationQuery['view'] = $activeView; // Preserve view tab
  ?>
  <div class="bg-white rounded-2xl shadow-lg p-6 mt-6">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
      <p class="text-sm text-slate-600">
        Menampilkan <span
          class="font-semibold text-slate-800"><?= (($pagination->currentPage - 1) * $pagination->perPage) + 1 ?></span>
        sampai <span
          class="font-semibold text-slate-800"><?= min($pagination->currentPage * $pagination->perPage, $pagination->total) ?></span>
        dari <span class="font-semibold text-slate-800"><?= $pagination->total ?></span> booking
      </p>
      <div class="flex gap-2 items-center">
        <!-- First Page -->
        <?php if ($pagination->currentPage > 1): ?>
          <?php $paginationQuery['page'] = 1; ?>
          <a href="/admin/bookings?<?= http_build_query($paginationQuery) ?>"
            class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
            Awal
          </a>
        <?php endif; ?>
        <!-- Previous -->
        <?php if ($pagination->currentPage > 1): ?>
          <?php $paginationQuery['page'] = $pagination->currentPage - 1; ?>
          <a href="/admin/bookings?<?= http_build_query($paginationQuery) ?>"
            class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
            ← Sebelumnya
          </a>
        <?php endif; ?>
        <!-- Page Numbers -->
        <div class="flex gap-1">
          <?php for ($i = 1; $i <= $pagination->lastPage; $i++): ?>
            <?php $paginationQuery['page'] = $i; ?>
            <a href="/admin/bookings?<?= http_build_query($paginationQuery) ?>" class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-semibold transition-all
                <?= $i === $pagination->currentPage
                  ? 'bg-emerald-600 text-white shadow-md'
                  : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
              <?= $i ?>
            </a>
          <?php endfor; ?>
        </div>
        <!-- Next -->
        <?php if ($pagination->currentPage < $pagination->lastPage): ?>
          <?php $paginationQuery['page'] = $pagination->currentPage + 1; ?>
          <a href="/admin/bookings?<?= http_build_query($paginationQuery) ?>"
            class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
            Selanjutnya →
          </a>
        <?php endif; ?>
        <!-- Last Page -->
        <?php if ($pagination->currentPage < $pagination->lastPage): ?>
          <?php $paginationQuery['page'] = $pagination->lastPage; ?>
          <a href="/admin/bookings?<?= http_build_query($paginationQuery) ?>"
            class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
            Akhir
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php endif; ?>