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
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Buat Booking Baru
      </h2>
      <span
        class="inline-flex px-4 py-2 rounded-lg font-semibold text-sm border-2 bg-emerald-100 text-emerald-800 border-emerald-300">
        Admin Create
      </span>
    </div>
    <p class="text-slate-600">Buat booking atas nama user tertentu</p>
  </div>
  <form action="/admin/bookings/store" method="post">
    <?= csrf_field() ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">

        <!-- PIC Selection -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
          <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Pilih PIC (Penanggung Jawab)
          </h3>
          <select name="user_id" required
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
            <option value="">-- Pilih User --</option>
            <?php foreach ($users as $user): ?>
              <option value="<?= $user->id_user ?>">
                <?= htmlspecialchars($user->nama) ?> (<?= htmlspecialchars($user->email) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <!-- Booking Details -->
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
                <select name="ruangan_id" required
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                  <option value="">-- Pilih Ruangan --</option>
                  <?php foreach ($rooms as $room): ?>
                    <option value="<?= $room->id_ruangan ?>">
                      <?= htmlspecialchars($room->nama_ruangan) ?>
                      (Min: <?= $room->kapasitas_min ?>, Max: <?= $room->kapasitas_max ?>)
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
                <input type="date" name="tanggal_penggunaan_ruang" required
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
                  <input type="time" name="waktu_mulai" required placeholder="Mulai"
                    class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                  <span class="flex items-center text-slate-400">-</span>
                  <input type="time" name="waktu_selesai" required placeholder="Selesai"
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
                <label class="block text-sm font-semibold text-slate-600 mb-2">Tujuan Penggunaan</label>
                <textarea name="tujuan" required rows="3" placeholder="Jelaskan tujuan penggunaan ruangan..."
                  class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all resize-none"></textarea>
              </div>
            </div>
          </div>
        </div>
        <!-- Submit Button -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
          <button type="submit"
            class="w-full bg-primary text-white px-8 py-4 rounded-xl hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl flex items-center justify-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Buat Booking (Langsung Verified)
          </button>
          <p class="text-sm text-slate-500 text-center mt-3">
            Booking akan langsung berstatus Verified. Anggota dapat ditambahkan setelah booking dibuat.
          </p>
        </div>
      </div>
      <!-- Sidebar -->
      <div class="space-y-6">
        <!-- Info Card -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl shadow-lg p-6 border-2 border-blue-200">
          <h3 class="font-bold text-blue-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Info Admin
          </h3>
          <ul class="space-y-2 text-sm text-blue-700">
            <li>• Booking dibuat atas nama user yang dipilih</li>
            <li>• Status langsung <strong>Verified</strong></li>
            <li>• Checkin code otomatis dibuat</li>
            <li>• Tambah anggota via halaman Edit</li>
          </ul>
        </div>
        <!-- Help Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
          <h3 class="font-bold text-slate-800 mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Langkah
          </h3>
          <ol class="space-y-3 text-sm text-slate-600">
            <li class="flex items-start">
              <span
                class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 font-semibold text-xs flex items-center justify-center mr-2">1</span>
              <span>Pilih user sebagai PIC</span>
            </li>
            <li class="flex items-start">
              <span
                class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 font-semibold text-xs flex items-center justify-center mr-2">2</span>
              <span>Pilih ruangan & isi detail</span>
            </li>
            <li class="flex items-start">
              <span
                class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 font-semibold text-xs flex items-center justify-center mr-2">3</span>
              <span>Klik buat booking</span>
            </li>
          </ol>
        </div>
      </div>
    </div>
  </form>
</div>