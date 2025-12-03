<?php

$statusOptions = [
  'available' => 'Available',
  'unavailable' => 'Unavailable',
  'adminOnly' => 'Admin Only',
];
$roomTypes = [
  'Audio Visual',
  'Telekonferensi',
  'Kreasi dan Rekreasi',
  'Baca Kelompok',
  'Koleksi Bahasa Prancis',
  'Bimbingan & Konseling',
  'Ruang Rapat',
];

$statusColors = [
  'available' => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
  'under_maintenance' => 'bg-yellow-100 text-yellow-800 border border-yellow-300',
  'unavailable' => 'bg-red-100 text-red-800 border border-red-300',
];
?>

<div class="p-6">
  <!-- Header -->
  <div class="mb-8 flex flex-col md:flex-row justify-between items-center">
    <div>
      <h2 class="text-3xl font-bold text-gray-900 mb-2">Manajemen Ruangan</h2>
      <p class="text-gray-600">Kelola dan monitor ruangan yang tersedia</p>
    </div>
    <p>
      <a href="/admin/rooms/create"
        class="flex gap-4 bg-primary shadow text-white mt-4 md:mt-0 px-8 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
          class="lucide lucide-plus-icon lucide-plus">
          <path d="M5 12h14" />
          <path d="M12 5v14" />
        </svg>
        Tambah Ruangan
      </a>
    </p>
  </div>

  <!-- Flash Messages -->
  <?php if ($message = flash('success')): ?>
    <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <?php if ($message = flash('error')): ?>
    <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
      <?= htmlspecialchars($message) ?>
    </div>
  <?php endif; ?>

  <!-- Filters Section -->
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
    <form method="get" action="/admin/rooms"
      class="flex flex-col justify-between items-start md:items-center gap-6 px-4 md:px-8">
      <div class="md:items-center gap-4 md:gap-8">
        <div class="flex flex-col md:flex-row gap-4">
          <!-- Keyword -->
          <label class="flex flex-col md:flex-row items-center gap-4 capitalize">
            <div class="items-center gap-4 hidden md:flex w-full">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-search-icon lucide-search size-5">
                <path d="m21 21-4.34-4.34" />
                <circle cx="11" cy="11" r="8" />
              </svg>
              <span class="text-sm">Kata kunci:</span>
            </div>
            <input type="text" name="keyword"
              class="w-full flex grow border border-gray-300 rounded-lg px-4 py-1 text-sm accent-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
              value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>" placeholder="Kata Kunci">
          </label>

          <!-- Room Type -->
          <label class="flex items-center gap-4">
            <div class="hidden md:flex">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-building-icon size-5">
                <rect width="16" height="20" x="4" y="2" rx="2" ry="2" />
                <path d="M9 22v-4h6v4" />
                <path d="M8 6h.01" />
                <path d="M16 6h.01" />
                <path d="M12 6h.01" />
                <path d="M12 10h.01" />
                <path d="M12 14h.01" />
                <path d="M16 10h.01" />
                <path d="M16 14h.01" />
                <path d="M8 10h.01" />
                <path d="M8 14h.01" />
              </svg>
              <span>
                Jenis Ruangan:
              </span>
            </div>

            <div class="relative">
              <button id="dropdownButton" type="button"
                class="flex items-center justify-center gap-4 bg-primary text-white border border-transparent shadow font-medium leading-5 px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition">
                <span class="capitalize">
                  <?= ($filters['jenis_ruangan'] ?? '') !== '' ? htmlspecialchars($filters['jenis_ruangan']) : 'Semua Jenis' ?>
                </span>

                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-chevron-down mt-0.5">
                  <path d="m6 9 6 6 6-6" />
                </svg>
              </button>

              <div id="dropdown"
                class="absolute mt-2 origin-top transition-all duration-150 ease-out scale-95 opacity-0 z-10 hidden bg-gray-100 border border-gray-200 rounded shadow">

                <ul class="p-2 text-sm font-medium space-y-1" aria-labelledby="dropdownButton">

                  <li class="px-4 py-2 rounded cursor-pointer hover:bg-gray-200 transition
                    <?php if (($filters['jenis_ruangan'] ?? '') === ''): ?> bg-gray-300 <?php endif; ?>" data-value="">
                    Semua Jenis
                  </li>

                  <?php foreach ($roomTypes as $type): ?>
                    <li class="px-4 py-2 rounded cursor-pointer hover:bg-gray-200 transition
                      <?php if (($filters['jenis_ruangan'] ?? '') === $type): ?> bg-gray-300 <?php endif; ?>"
                      data-value="<?= htmlspecialchars($type) ?>">
                      <?= htmlspecialchars($type) ?>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <input type="hidden" id="statusSelected" name="jenis_ruangan"
                value="<?= $filters['jenis_ruangan'] ?? '' ?>">
            </div>
          </label>


          <!-- Status -->
          <label class="flex items-center gap-4">
            <div class="hidden md:flex">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-chart-column-decreasing-icon lucide-chart-column-decreasing size-5">
                <path d="M13 17V9" />
                <path d="M18 17v-3" />
                <path d="M3 3v16a2 2 0 0 0 2 2h16" />
                <path d="M8 17V5" />
              </svg>
              <span>
                Status:
              </span>
            </div>
            <div class="relative">
              <button id="dropdownButton2" type="button" data-dropdown-toggle="dropdown"
                class="flex items-center justify-center gap-4 bg-primary text-white box-border border border-transparent shadow font-medium leading-5 px-4 py-2 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition">
                <span
                  class="capitalize"><?= htmlspecialchars(($filters['status_ruangan'] ?? '') !== '' ? ucwords(str_replace('_', ' ', $filters['status_ruangan'])) : 'Semua Status') ?></span>
                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" width="24" height="24" viewBox="0 0 24 24"
                  fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-chevron-down-icon lucide-chevron-down mt-0.5">
                  <path d="m6 9 6 6 6-6" />
                </svg>
              </button>
              <div id="dropdown2"
                class="absolute mt-2 origin-top transition-all duration-150 ease-out scale-95 opacity-0 z-10 hidden bg-gray-100 border border-gray-200 rounded shadow">
                <ul class="p-2 text-sm font-medium space-y-1" aria-labelledby="dropdownButton">
                  <li class="px-4 py-2 rounded cursor-pointer hover:bg-gray-200 transition
                  <?php if (($filters['status_ruangan'] ?? '') === ''): ?> bg-gray-300 <?php endif; ?>" data-value="">
                    Semua
                  </li>
                  <?php foreach ($statusOptions as $status): ?>
                    <li class="px-4 py-2 rounded cursor-pointer hover:bg-gray-200 transition
                    <?php if (($filters['status_ruangan'] ?? '') === $status): ?> bg-gray-300 <?php endif; ?>"
                      data-value="<?= htmlspecialchars($status) ?>">
                      <?= htmlspecialchars(ucwords(str_replace('_', ' ', $status))) ?>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <input type="hidden" id="statusSelected2" name="status_ruangan"
                value="<?= $filters['status_ruangan'] ?? '' ?>">
            </div>
          </label>
        </div>
      </div>
      <div class="flex gap-4">

        <div class="flex items-center gap-8 text-sm">
          <button type="submit"
            class="cursor-pointer px-4 py-2 border-2 border-emerald-600 rounded-lg font-medium text-emerald-50 bg-emerald-600 hover:bg-emerald-700 tracking-wider transition-all focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
            Terapkan
          </button>
          <a href="/admin/rooms"
            class="cursor-pointer px-4 py-2 border-2 border-zinc-300 rounded-lg font-medium text-zinc-700 hover:bg-zinc-50 transition-all tracking-wider focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2">
            Bersihkan
          </a>
        </div>

      </div>
    </form>
  </section>

  <?php if (empty($rooms)): ?>
    <!-- Empty State -->
    <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-100">
      <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
      </svg>
      <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Ruangan</h3>
      <p class="text-gray-600">Belum ada ruangan yang terdaftar dalam sistem.</p>
    </div>
  <?php else: ?>
    <!-- Rooms Table -->
    <div class="bg-white rounded-md shadow-md overflow-x-auto border border-gray-200">
      <table class="w-full text-sm text-left">
        <thead class="bg-linear-to-br from-emerald-600 to-emerald-800">
          <tr
            class=" *:px-6 *:py-3  *:text-left *:text-regular *:font-semibold *:text-gray-50 *:capitalize *:tracking-wider *:whitespace-nowrap">
            <th scope="col">ID</th>
            <th scope="col">Nama Ruangan</th>
            <th scope="col">Kapasitas</th>
            <th scope="col">Jenis Ruangan</th>
            <th scope="col">Status</th>
            <th scope="col">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php foreach ($rooms as $room): ?>
            <tr class="hover:bg-gray-200 odd:bg-gray-50 even:bg-gray-100 transition-colors border-b border-gray-200">
              <th scope="row" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                <?= htmlspecialchars((string) $room->id_ruangan) ?>
              </th>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                <?= htmlspecialchars($room->nama_ruangan) ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                <?= htmlspecialchars((string) $room->kapasitas_min) ?> -
                <?= htmlspecialchars((string) $room->kapasitas_max) ?> orang
              </td>
              <td class="px-6 py-4 text-sm text-gray-700">
                <?= htmlspecialchars($room->jenis_ruangan) ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow <?= $statusColors[$room->status_ruangan] ?? 'bg-gray-100 text-gray-800' ?>">
                  <?= htmlspecialchars(ucwords(str_replace('_', ' ', $room->status_ruangan))) ?>
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm space-y-2 flex gap-2 flex-wrap">
                <a href="/admin/rooms/show?id=<?= $room->id_ruangan ?>"
                  class="items-center px-4 py-2 border border-slate-300 rounded-lg font-medium text-slate-700 hover:bg-slate-50 transition-colors flex gap-2">
                  Lihat
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-eye size-4">
                    <path
                      d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                    <circle cx="12" cy="12" r="3" />
                  </svg>
                </a>
                <a href="/admin/rooms/edit?id=<?= $room->id_ruangan ?>"
                  class="inline-flex items-center px-4 py-2 bg-orange-200 rounded-lg font-medium text-orange-900 hover:bg-orange-400 transition-colors cursor-pointer border-2 border-orange-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 text-sm">
                  Edit
                </a>
                <form method="post" action="/admin/rooms/delete" class="inline">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id_ruangan" value="<?= $room->id_ruangan ?>">
                  <button type="submit" onclick="return confirm('Hapus ruangan ini?')"
                    class="inline-flex items-center px-4 py-2 bg-red-200 rounded-lg font-medium text-red-900 hover:bg-red-400 transition-colors cursor-pointer border-2 border-red-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 text-sm">
                    Hapus
                  </button>
                </form>
                <?php if ($room->status_ruangan !== 'unavailable'): ?>
                  <form method="post" action="/admin/rooms/deactivate" class="inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_ruangan" value="<?= $room->id_ruangan ?>">
                    <button type="submit"
                      class="inline-flex items-center px-4 py-2 bg-yellow-200 rounded-lg font-medium text-yellow-900 hover:bg-yellow-400 transition-colors cursor-pointer border-2 border-yellow-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 text-sm">
                      Nonaktifkan
                    </button>
                  </form>
                <?php else: ?>
                  <form method="post" action="/admin/rooms/activate" class="inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_ruangan" value="<?= $room->id_ruangan ?>">
                    <button type="submit"
                      class="inline-flex items-center px-4 py-2 bg-emerald-200 rounded-lg font-medium text-emerald-900 hover:bg-emerald-400 transition-colors cursor-pointer border-2 border-emerald-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 text-sm">
                      Aktifkan
                    </button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- Pagination -->
<div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
  <div class="flex items-center justify-between">
    <p class="text-sm text-slate-600">
      Showing <span class="font-semibold"><?= (($pagination->currentPage - 1) * $pagination->perPage) + 1 ?></span>
      to <span
        class="font-semibold"><?= min($pagination->currentPage * $pagination->perPage, $pagination->total) ?></span>
      of <span class="font-semibold"><?= $pagination->total ?></span> results
    </p>

    <div class="flex gap-2">
      <?php if ($pagination->currentPage > 1): ?>
        <a href="/admin/rooms?page=<?= $pagination->currentPage - 1 ?>"
          class="px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
          Previous
        </a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= ceil($pagination->total / $pagination->perPage); $i++): ?>
        <a href="/admin/rooms?page=<?= $i ?>"
          class="px-3 py-2 rounded-lg text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 text-white
                  <?= $i === $pagination->currentPage ? 'bg-emerald-600 hover:bg-emerald-700' : 'border border-slate-300 text-slate-700 hover:bg-slate-100' ?>">
          <?= $i ?>
        </a>
      <?php endfor; ?>

      <?php if ($pagination->currentPage < ceil($pagination->total / $pagination->perPage)): ?>
        <a href="/admin/rooms?page=<?= $pagination->currentPage + 1 ?>"
          class="px-3 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 transition-colors">
          Next
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>