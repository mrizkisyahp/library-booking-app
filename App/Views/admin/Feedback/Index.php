<?php
dump($feedbacks);
?>

<div class="p-6">
  <div class="mb-8 flex flex-col md:flex-row justify-between items-center">
    <div>
      <h1 class="text-3xl font-bold text-gray-900 mb-2">Feedback Pengguna</h1>
      <p class="text-gray-600">Monitor seluruh feedback yang diberikan oleh pengguna</p>
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
    <form method="get" action="/admin/feedback"
      class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 px-4 w-full">
      <div class="flex flex-col gap-4 w-full">
        <div class="flex gap-6 justify-start w-full">
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
          <label class="flex flex-col md:flex-row items-center capitalize gap-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
              class="lucide lucide-square-star-icon lucide-square-star">
              <path
                d="M11.035 7.69a1 1 0 0 1 1.909.024l.737 1.452a1 1 0 0 0 .737.535l1.634.256a1 1 0 0 1 .588 1.806l-1.172 1.168a1 1 0 0 0-.282.866l.259 1.613a1 1 0 0 1-1.541 1.134l-1.465-.75a1 1 0 0 0-.912 0l-1.465.75a1 1 0 0 1-1.539-1.133l.258-1.613a1 1 0 0 0-.282-.866l-1.156-1.153a1 1 0 0 1 .572-1.822l1.633-.256a1 1 0 0 0 .737-.535z" />
              <rect x="3" y="3" width="18" height="18" rx="2" />
            </svg>
            Rating
            <input type="number" name="rating" min="1" max="5"
              class="w-fit flex grow border border-gray-300 rounded-lg px-4 py-1 text-sm accent-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
              value="<?= htmlspecialchars($filters['rating'] ?? '') ?>">
          </label>
          <label class="flex flex-col md:flex-row items-center capitalize gap-4">
            <div class=" text-nowrap">
              Tanggal Penggunaan
            </div>
            <input type="date" name="tanggal"
              class="w-full flex grow border border-gray-300 rounded-lg px-4 py-1 text-sm accent-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
              value="<?= htmlspecialchars($filters['tanggal'] ?? '') ?>">
          </label>
        </div>

        <div class="flex gap-6 w-full">
        </div>
        <div>
          <button type="submit"
            class="cursor-pointer px-4 py-2 border-2 border-emerald-600 rounded-lg font-medium text-emerald-50 bg-emerald-600 hover:bg-emerald-700 tracking-wider transition-all focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
            Terapkan
          </button>

          <a href="/admin/feedback"
            class="cursor-pointer px-4 py-2 border-2 border-zinc-300 rounded-lg font-medium text-zinc-700 hover:bg-zinc-50 transition-all tracking-wider focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2">
            Bersihkan
          </a>
        </div>
      </div>
    </form>
  </section>

  <section>
    <?php if (empty($feedbacks)): ?>
      <!-- Empty State -->
      <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-100">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Feedback</h3>
        <p class="text-gray-600">Belum ada User yang memberikan feedback.</p>
      </div>
    <?php else: ?>
      <div class="bg-white rounded-md shadow-md overflow-x-auto border border-gray-200">
        <table class="w-full text-sm text-left">
          <thead class="bg-linear-to-br from-emerald-600 to-emerald-800">
            <tr
              class=" *:px-6 *:py-3  *:text-left *:text-regular *:font-semibold *:text-gray-50 *:capitalize *:tracking-wider *:whitespace-nowrap">
              <th scope="col">User</th>
              <th scope="col">Ruangan</th>
              <th scope="col">Rating</th>
              <th scope="col">Tanggal</th>
              <th scope="col">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php foreach ($feedbacks as $row): ?>
              <tr class="hover:bg-gray-200 odd:bg-gray-50 even:bg-gray-100 transition-colors border-b border-gray-200">
                <th scope="row" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  <?= htmlspecialchars($row->nama ?? '-') ?>
                </th>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                  <?= htmlspecialchars($row->nama_ruangan ?? '-') ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                  <?= htmlspecialchars((string) $row->rating) ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                  <?= htmlspecialchars($row->tanggal_penggunaan_ruang ?? '-') ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                  <a href="/admin/feedback/detail?id=<?= (int) $row->id_feedback ?>">Detail</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
  </section>
</div>