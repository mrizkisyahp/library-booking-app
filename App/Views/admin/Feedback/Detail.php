<?php
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;

/** @var array $feedback */
/** @var Booking|null $booking */
/** @var Room|null $room */
/** @var User|null $pic */
/** @var User|null $feedbackUser */
$members = $members ?? [];
?>

<div class="max-w-5xl mx-auto space-y-6">
  <div class="mb-2">
    <a href="/admin/feedback"
      class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold group">
      <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
        stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali ke daftar feedback
    </a>
  </div>

  <div class="bg-white rounded-2xl shadow-lg p-8">
    <div class="flex items-center justify-between mb-2">
      <div>
        <p class="text-sm text-slate-500 uppercase">Detail Booking</p>
        <h1 class="text-3xl font-bold text-slate-800">#<?= htmlspecialchars((string) $booking->id_booking) ?></h1>
      </div>
      <span
        class="inline-flex px-4 py-2 rounded-lg font-semibold text-sm border <?= $statusColors[$booking->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' ?>">
        <?= htmlspecialchars(ucfirst($booking->status)) ?>
      </span>
    </div>
    <p class="text-slate-600">Periksa detail booking sebelum melakukan verifikasi.</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="lg:col-span-2 space-y-2">
      <div class="bg-white rounded-2xl shadow-lg p-8 space-y-4">
        <h2 class="text-xl font-bold text-slate-800 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-file-icon lucide-file mr-2 text-emerald-600">
            <path
              d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z" />
            <path d="M14 2v5a1 1 0 0 0 1 1h5" />
          </svg>
          Detail Feedback
        </h2>

        <div class="space-y-4">
          <div class="p-4 bg-slate-50 rounded-xl">
            <p class="text-xs font-semibold text-slate-500 uppercase">Ruangan</p>
            <p class="text-lg font-bold text-slate-800">
              <?= htmlspecialchars($room?->nama_ruangan ?? 'Tidak diketahui') ?>
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-4">
            <div class="p-4 bg-slate-50 rounded-xl">
              <p class="text-xs font-semibold text-slate-500 uppercase">Tanggal Penggunaan</p>
              <p class="text-lg font-bold text-slate-800">
                <?= htmlspecialchars($booking->tanggal_penggunaan_ruang ?? '-') ?>
              </p>
            </div>
          </div>

          <div class="space-y-4">
            <div class="p-4 bg-slate-50 rounded-xl">
              <p class="text-xs font-semibold text-slate-500 uppercase">Waktu</p>
              <p class="text-lg font-bold text-slate-800">
                <?= htmlspecialchars(($booking->waktu_mulai ?? '') . ' - ' . ($booking->waktu_selesai ?? '')) ?>
              </p>
            </div>
          </div>
        </div>

        <div class="space-y-4">
          <div class="p-4 bg-slate-50 rounded-xl">
            <p class="text-xs font-semibold text-slate-500 uppercase">PIC</p>
            <p class="text-lg font-bold text-slate-800 capitalize">
              <?= htmlspecialchars($pic?->nama ?? '-') ?>
            </p>
            <p class="text-sm text-slate-600 capitalize">
              Tanggal pengisian: <?= htmlspecialchars($feedback['created_at'] ?? '-') ?>
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="space-y-4 grid col-span-2 w-full">
            <div class="p-4 bg-slate-50 rounded-xl">
              <p class="text-xs font-semibold text-slate-500 uppercase">Komentar Penggunaan</p>
              <p class="text-lg font-bold text-slate-800 capitalize">
                <?= nl2br(htmlspecialchars($feedback['komentar'] ?? '-')) ?>
              </p>
            </div>
          </div>

          <div class="space-y-4 col-span-2">
            <div class="p-4 bg-slate-50 rounded-xl">
              <p class="text-xs font-semibold text-slate-500 uppercase">Rating Ruangan</p>
              <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-star-icon lucide-star size-4 text-emerald-600">
                  <path
                    d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z" />
                </svg>
                <p class="text-lg font-bold text-slate-800">
                  <?= htmlspecialchars((string) ($feedback['rating'] ?? '-')) ?>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <section class="bg-white shadow rounded-lg p-6 mb-8 border border-gray-100">
    <?php if (empty($members)): ?>
      <!-- Empty State -->
      <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-100">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Anggota Tercatat</h3>
        <p class="text-gray-600">Mungkin terjadi kesalahan saat pencatatan, silakan hubungi admin.</p>
      </div> <?php else: ?>
      <div class="bg-white rounded-md shadow-md overflow-x-auto border border-gray-200">
        <table class="w-full text-sm text-left">
          <thead class="bg-linear-to-br from-emerald-600 to-emerald-800">
            <tr
              class=" *:px-6 *:py-3  *:text-left *:text-regular *:font-semibold *:text-gray-50 *:capitalize *:tracking-wider *:whitespace-nowrap">
              <th scope="col">Nama</th>
              <th scope="col">Email</th>
              <th scope="col">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php foreach ($members as $member): ?>
              <tr class="hover:bg-gray-200 odd:bg-gray-50 even:bg-gray-100 transition-colors border-b border-gray-200">
                <th scope="row" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 capitalize">
                  <?= htmlspecialchars($member['nama'] ?? '-') ?>
                </th>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  <?= htmlspecialchars($member['email'] ?? '-') ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                  <?= !empty($member['is_owner']) ? 'PIC' : 'Anggota' ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </section>
</div>