<?php

use App\Core\Csrf;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;

/** @var Booking $booking */
/** @var Room|null $room */
/** @var User|null $pic */
$members = $members ?? [];

$statusColors = [
  'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
  'verified' => 'bg-blue-100 text-blue-800 border-blue-200',
];

$roomName = $room?->nama_ruangan ?? ('Ruangan #' . htmlspecialchars((string) $booking->ruangan_id));
$picName = $pic?->nama ?? 'Pengguna #' . htmlspecialchars((string) $booking->user_id);
$picContact = $pic?->email ?? '-';
$picKubaca = $pic?->kubaca_img ?? null;
$picIdNumber = $pic?->nim ?: $pic?->nip ?: '-';
?>

<div class="max-w-5xl mx-auto space-y-6">
  <div class="mb-2">
    <a href="/admin/bookings"
      class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold group">
      <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
        stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali ke daftar booking
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

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
      <div class="bg-white rounded-2xl shadow-lg p-8 space-y-5">
        <h2 class="text-xl font-bold text-slate-800 flex items-center">
          <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          Ringkasan Booking
        </h2>

        <div class="space-y-4">
          <div class="p-4 bg-slate-50 rounded-xl">
            <p class="text-xs font-semibold text-slate-500 uppercase">Ruangan</p>
            <p class="text-lg font-bold text-slate-800"><?= htmlspecialchars($roomName) ?></p>
            <?php if ($room): ?>
              <p class="text-sm text-slate-600">Kapasitas: <?= htmlspecialchars((string) $room->kapasitas_min) ?> -
                <?= htmlspecialchars((string) $room->kapasitas_max) ?> orang
              </p>
            <?php endif; ?>
          </div>

          <div class="p-4 bg-slate-50 rounded-xl">
            <p class="text-xs font-semibold text-slate-500 uppercase">PIC</p>
            <p class="text-lg font-bold text-slate-800"><?= htmlspecialchars($picName) ?></p>
            <p class="text-sm text-slate-600"><?= htmlspecialchars($picContact) ?></p>
            <div class="mt-2 flex items-center gap-3">
              <p class="text-xs text-slate-500 uppercase"><?= $pic?->nim ? 'NIM' : 'NIP' ?>:</p>
              <p class="text-sm font-semibold text-slate-700"><?= htmlspecialchars($picIdNumber) ?></p>
              <?php if ($picKubaca): ?>
                <button
                  class="view-button text-emerald-600 hover:text-emerald-700 text-sm font-semibold flex items-center"
                  data-img="uploads/kubaca/<?= htmlspecialchars($picKubaca) ?>"
                  data-nim="<?= htmlspecialchars($pic?->nim ?? $pic?->nip ?? '') ?>"
                  data-nama="<?= htmlspecialchars($picName) ?>">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  Lihat KuBaca
                </button>
              <?php else: ?>
                <span class="text-xs text-slate-400">KuBaca belum diunggah</span>
              <?php endif; ?>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-slate-50 rounded-xl">
              <p class="text-xs font-semibold text-slate-500 uppercase">Tanggal Penggunaan</p>
              <p class="text-lg font-bold text-slate-800">
                <?= date('l, d F Y', strtotime($booking->tanggal_penggunaan_ruang)) ?>
              </p>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl">
              <p class="text-xs font-semibold text-slate-500 uppercase">Waktu</p>
              <p class="text-lg font-bold text-slate-800">
                <?= htmlspecialchars(substr($booking->waktu_mulai, 0, 5)) ?> -
                <?= htmlspecialchars(substr($booking->waktu_selesai, 0, 5)) ?>
              </p>
            </div>
          </div>

          <div class="p-4 bg-slate-50 rounded-xl">
            <p class="text-xs font-semibold text-slate-500 uppercase">Tujuan</p>
            <p class="text-sm text-slate-700"><?= nl2br(htmlspecialchars($booking->tujuan ?? '-')) ?></p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-bold text-slate-800 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Anggota
          </h3>
          <span class="text-sm text-slate-500"><?= count($members) ?> anggota terdaftar</span>
        </div>

        <?php if (empty($members)): ?>
          <p class="text-sm text-slate-500">Belum ada anggota yang ditambahkan.</p>
        <?php else: ?>
          <div class="overflow-hidden border border-slate-100 rounded-2xl">
            <table class="w-full">
              <thead class="bg-slate-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Nama</th>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Email</th>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 bg-white">
                <?php foreach ($members as $member): ?>
                  <tr>
                    <td class="px-4 py-3 text-sm font-semibold text-slate-800">
                      <?= htmlspecialchars($member['nama'] ?? '-') ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-600"><?= htmlspecialchars($member['email'] ?? '-') ?></td>
                    <td class="px-4 py-3 text-sm">
                      <?php if (!empty($member['is_owner'])): ?>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">PIC</span>
                      <?php else: ?>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700">Anggota</span>
                      <?php endif; ?>
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
      <div class="bg-white rounded-2xl shadow-lg p-6 space-y-4">
        <h3 class="text-lg font-bold text-slate-800">Kode Akses</h3>
        <div>
          <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Invite Token</p>
          <div class="p-4 bg-slate-900 rounded-xl text-white font-mono tracking-widest text-center text-lg">
            <?= htmlspecialchars($booking->invite_token ?? '-') ?>
          </div>
        </div>
        <div>
          <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Kode Check-in</p>
          <div class="p-4 bg-slate-100 rounded-xl font-mono tracking-widest text-center text-lg text-slate-800">
            <?= $booking->checkin_code ? htmlspecialchars($booking->checkin_code) : 'Belum tersedia' ?>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-lg p-6 space-y-5">
        <h3 class="text-lg font-bold text-slate-800">Aksi</h3>

        <?php if ($booking->status === 'pending'): ?>
          <form action="/admin/bookings/verify" method="post" class="space-y-3">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
            <button type="submit"
              class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors">
              Konfirmasi Booking
            </button>
          </form>
        <?php elseif ($booking->status === 'verified'): ?>
          <form action="/admin/bookings/activate" method="post" class="space-y-3">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
            <label class="block text-sm font-semibold text-slate-700">
              Masukkan Kode Check-in
              <input type="text" name="checkin_code"
                class="mt-2 w-full border border-slate-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                placeholder="CT-XXXX" required>
            </label>
            <button type="submit"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors">
              Validasi Check-in
            </button>
          </form>
        <?php elseif ($booking->status === 'active'): ?>
          <form action="/admin/bookings/complete" method="post" class="space-y-3">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
            <button type="submit"
              class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors">
              Tandai Selesai
            </button>
          </form>
        <?php else: ?>
          <p class="text-sm text-slate-500">Tidak ada aksi lanjutan untuk status saat ini.</p>
        <?php endif; ?>

        <div class="pt-4 border-t border-slate-200">
          <?php if ($booking->status !== 'draft'): ?>
            <form action="/admin/bookings/cancel" method="post" class="space-y-3">
              <?= csrf_field() ?>
              <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
              <label class="block text-sm font-semibold text-slate-700">
                Batalkan Booking (opsional: alasan)
                <textarea name="reason" rows="3"
                  class="mt-2 w-full border border-slate-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-rose-500"
                  placeholder="Alasan pembatalan (opsional)"></textarea>
              </label>
              <button type="submit"
                class="w-full bg-rose-600 hover:bg-rose-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors">
                Batalkan Booking
              </button>
            </form>
          <?php endif; ?>
        </div>

        <a href="/admin/bookings"
          class="inline-flex items-center justify-center w-full border border-slate-300 text-slate-700 font-semibold py-3 px-4 rounded-xl hover:bg-slate-50 transition-colors">
          Kembali ke daftar
        </a>
      </div>
    </div>
  </div>
</div>

<div id="imagePopUp" class="hidden fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-md z-50">
  <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-4 transition-all scale-95 opacity-0 duration-300">

    <!-- HELP OVER HERE -->

    <img id="popUpImage" src="<?= !empty($picKubaca) ? $picKubaca : '' ?> " alt="Pop-up Image"
      class="w-full h-64 object-cover rounded-md mb-4">
    <div class="flex items-center justify-start gap-4">
      <p><?php if ($pic?->nim ?? $pic?->nip): ?>
          NIM:
        <?php else: ?>
          NIP:
        <?php endif; ?>
      </p>
      <p id="popUpId" class="text-sm font-semibold text-gray-600">
        <?= htmlspecialchars($pic?->nim ?? $pic?->nip ?? '') ?>
      </p>
    </div>
    <div class="flex items-center justify-start gap-4">
      <p>
        Nama:
      </p>
      <p id="popUpNama" class="text-sm font-semibold text-gray-600 capitalize"><?= htmlspecialchars($picName) ?></p>
    </div>
    <button id="closePopUp"
      class="mt-4 bg-emerald-600 text-white px-4 py-2 rounded-md hover:bg-emerald-800 w-full transition-all">
      Tutup
    </button>
  </div>
</div>