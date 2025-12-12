<div class="max-w-5xl mx-auto">
  <!-- Back Button -->
  <div class="mb-6">
    <a href="/admin/bookings"
      class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold group">
      <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
        stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali ke Daftar Booking
    </a>
  </div>
  <!-- Page Header -->
  <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-3xl font-bold text-slate-800 flex items-center">
        <svg class="w-8 h-8 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        Edit Booking #<?= $booking->id_booking ?>
      </h2>
      <span class="inline-flex px-4 py-2 rounded-lg font-semibold text-sm border-2 
        <?php if ($booking->status === 'verified'): ?>bg-emerald-100 text-emerald-800 border-emerald-300
        <?php elseif ($booking->status === 'active'): ?>bg-blue-100 text-blue-800 border-blue-300
        <?php elseif ($booking->status === 'draft'): ?>bg-gray-100 text-gray-800 border-gray-300
        <?php elseif ($booking->status === 'pending'): ?>bg-yellow-100 text-yellow-800 border-yellow-300
        <?php else: ?>bg-red-100 text-red-800 border-red-300<?php endif; ?>">
        <?= htmlspecialchars(ucfirst($booking->status)) ?>
      </span>
    </div>
    <p class="text-slate-600">PIC: <?= htmlspecialchars($booking->nama ?? 'Unknown') ?></p>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">

      <!-- Edit Booking Form -->
      <form action="/admin/bookings/update" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="booking_id" value="<?= $booking->id_booking ?>">

        <div class="bg-white rounded-2xl shadow-lg p-8">
          <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Detail Booking
          </h3>
          <div class="space-y-4">
            <!-- Room -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <svg class="w-5 h-5 mr-3 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Ruangan</label>
                <select name="ruangan_id"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                  <?php foreach ($rooms as $room): ?>
                    <option value="<?= $room->id_ruangan ?>" <?= $booking->ruangan_id == $room->id_ruangan ? 'selected' : '' ?>>
                      <?= htmlspecialchars($room->nama_ruangan) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <!-- Date -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <svg class="w-5 h-5 mr-3 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Tanggal Penggunaan</label>
                <input type="date" name="tanggal_penggunaan_ruang" value="<?= $booking->tanggal_penggunaan_ruang ?>"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
              </div>
            </div>
            <!-- Time -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <svg class="w-5 h-5 mr-3 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Waktu</label>
                <div class="flex gap-4">
                  <input type="time" name="waktu_mulai" value="<?= substr($booking->waktu_mulai, 0, 5) ?>"
                    class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                  <span class="flex items-center text-slate-400">-</span>
                  <input type="time" name="waktu_selesai" value="<?= substr($booking->waktu_selesai, 0, 5) ?>"
                    class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                </div>
              </div>
            </div>
            <!-- Purpose -->
            <div class="flex items-start p-4 bg-slate-50 rounded-xl">
              <svg class="w-5 h-5 mr-3 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-2">Tujuan</label>
                <textarea name="tujuan" rows="3"
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all resize-none"><?= htmlspecialchars($booking->tujuan) ?></textarea>
              </div>
            </div>
          </div>
          <button type="submit"
            class="mt-6 w-full bg-emerald-600 text-white px-8 py-4 rounded-xl hover:bg-emerald-700 transition-all font-semibold shadow-lg flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Simpan Perubahan
          </button>
        </div>
      </form>
      <!-- Members Section -->
      <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-bold text-slate-800 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Daftar Anggota
          </h3>
          <div class="text-sm font-semibold px-3 py-1 rounded-full bg-emerald-100 text-emerald-700">
            <?= (int) ($booking->current_members ?? 1) ?> /
            <?= ($booking->maximum_members ?? 0) > 0 ? (int) $booking->maximum_members : '∞' ?> peserta
            <?php if (($booking->required_members ?? 0) > 0): ?>
              · Min <?= (int) $booking->required_members ?>
            <?php endif; ?>
          </div>
        </div>
        <?php if ($members->total == 0): ?>
          <div class="text-center py-8">
            <svg class="w-16 h-16 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <p class="text-slate-600">Belum ada anggota</p>
          </div>
        <?php else: ?>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
            <?php foreach ($members->items as $member): ?>
              <div class="flex items-center p-3 bg-slate-50 rounded-xl">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                  <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-semibold text-slate-800 truncate"><?= htmlspecialchars($member['nama'] ?? 'Unknown') ?></p>
                  <p class="text-sm text-slate-500 truncate"><?= htmlspecialchars($member['email'] ?? '') ?></p>
                  <?php if (!empty($member['is_owner'])): ?>
                    <span class="text-xs text-emerald-600 font-semibold">⭐ PIC</span>
                  <?php endif; ?>
                </div>
                <?php if (empty($member['is_owner'])): ?>
                  <form action="/admin/bookings/kick" method="post" class="ml-2"
                    onsubmit="return confirm('Keluarkan anggota ini?');">
                    <?= csrf_field() ?>
                    <input type="hidden" name="booking_id" value="<?= $booking->id_booking ?>">
                    <input type="hidden" name="user_id" value="<?= $member['id_user'] ?>">
                    <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                      title="Keluarkan">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                    </button>
                  </form>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>

          <!-- Pagination -->
          <?php if ($allMembers->lastPage > 1): ?>
            <?php
            $pagination = $allMembers;
            $paginationQuery = $_GET;
            $paginationQuery['id'] = $booking->id_booking;
            ?>
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-4 mb-6 p-4 bg-slate-50 rounded-xl">
              <p class="text-sm text-slate-600">
                Menampilkan <span
                  class="font-semibold text-slate-800"><?= (($pagination->currentPage - 1) * $pagination->perPage) + 1 ?></span>
                sampai <span
                  class="font-semibold text-slate-800"><?= min($pagination->currentPage * $pagination->perPage, $pagination->total) ?></span>
                dari <span class="font-semibold text-slate-800"><?= $pagination->total ?></span> anggota
              </p>
              <div class="flex gap-2 items-center">
                <?php if ($pagination->currentPage > 1): ?>
                  <?php $paginationQuery['page'] = 1; ?>
                  <a href="/admin/bookings/edit?<?= http_build_query($paginationQuery) ?>"
                    class="px-3 py-1 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-100 transition-colors">Awal</a>
                <?php endif; ?>
                <?php if ($pagination->currentPage > 1): ?>
                  <?php $paginationQuery['page'] = $pagination->currentPage - 1; ?>
                  <a href="/admin/bookings/edit?<?= http_build_query($paginationQuery) ?>"
                    class="px-3 py-1 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-100 transition-colors">←</a>
                <?php endif; ?>
                <div class="flex gap-1">
                  <?php for ($i = 1; $i <= $pagination->lastPage; $i++): ?>
                    <?php $paginationQuery['page'] = $i; ?>
                    <a href="/admin/bookings/edit?<?= http_build_query($paginationQuery) ?>"
                      class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-semibold transition-all <?= $i === $pagination->currentPage ? 'bg-emerald-600 text-white' : 'bg-white text-slate-700 hover:bg-slate-100' ?>">
                      <?= $i ?>
                    </a>
                  <?php endfor; ?>
                </div>
                <?php if ($pagination->currentPage < $pagination->lastPage): ?>
                  <?php $paginationQuery['page'] = $pagination->currentPage + 1; ?>
                  <a href="/admin/bookings/edit?<?= http_build_query($paginationQuery) ?>"
                    class="px-3 py-1 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-100 transition-colors">→</a>
                <?php endif; ?>
                <?php if ($pagination->currentPage < $pagination->lastPage): ?>
                  <?php $paginationQuery['page'] = $pagination->lastPage; ?>
                  <a href="/admin/bookings/edit?<?= http_build_query($paginationQuery) ?>"
                    class="px-3 py-1 border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-100 transition-colors">Akhir</a>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <!-- Add Member Form -->
        <form action="/admin/bookings/add" method="post" class="border-t pt-6">
          <?= csrf_field() ?>
          <input type="hidden" name="booking_id" value="<?= $booking->id_booking ?>">
          <label class="block text-sm font-semibold text-slate-700 mb-2">Tambah Anggota</label>
          <div class="flex gap-3">
            <input type="email" name="member_email" required placeholder="Email anggota"
              class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
            <button type="submit"
              class="bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700 transition-all font-semibold whitespace-nowrap">
              <svg class="inline-block w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Tambah
            </button>
          </div>
        </form>
      </div>
      <!-- Delete Button -->
      <div class="bg-white rounded-2xl shadow-lg p-8">
        <form action="/admin/bookings/delete" method="post" onsubmit="return confirm('Yakin hapus booking ini?');">
          <?= csrf_field() ?>
          <input type="hidden" name="booking_id" value="<?= $booking->id_booking ?>">
          <button type="submit"
            class="w-full bg-red-500 text-white px-8 py-4 rounded-xl hover:bg-red-600 transition-all font-semibold shadow-lg flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Hapus Booking
          </button>
        </form>
      </div>
    </div>
    <!-- Sidebar -->
    <div class="space-y-6">
      <!-- Checkin Code -->
      <?php if (!empty($booking->checkin_code)): ?>
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl shadow-lg p-6 border-2 border-blue-200">
          <h3 class="font-bold text-blue-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
            Kode Check-in
          </h3>
          <p class="text-3xl font-mono font-bold text-blue-800 text-center py-4 bg-white rounded-lg">
            <?= htmlspecialchars($booking->checkin_code) ?>
          </p>
        </div>
      <?php endif; ?>
      <!-- Invite Token -->
      <?php if (!empty($booking->invite_token)): ?>
        <div
          class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl shadow-lg p-6 border-2 border-emerald-200">
          <h3 class="font-bold text-emerald-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
            </svg>
            Link Undangan
          </h3>
          <div class="bg-white rounded-lg p-3 mb-3 border border-emerald-200">
            <p class="text-xs font-mono text-slate-600 break-all"><?= htmlspecialchars($booking->invite_token) ?></p>
          </div>
        </div>
      <?php endif; ?>
      <!-- Booking Info -->
      <div class="bg-white rounded-2xl shadow-lg p-6">
        <h3 class="font-bold text-slate-800 mb-3">Info Booking</h3>
        <ul class="space-y-2 text-sm text-slate-600">
          <li>ID: #<?= $booking->id_booking ?></li>
          <li>Dibuat: <?= $booking->tanggal_booking ?></li>
          <li>Status: <?= ucfirst($booking->status) ?></li>
        </ul>
      </div>
    </div>
  </div>
</div>