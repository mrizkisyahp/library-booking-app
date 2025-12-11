<div class="max-w-6xl mx-auto">
  <!-- Back Button -->
  <div class="mb-6">
    <a href="/rooms" class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold group">
      <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
        stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali ke Daftar Ruangan
    </a>
  </div>

  <!-- Room Header -->
  <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6">
      <div>
        <h2 class="text-3xl font-bold text-slate-800 mb-3">
          <?= htmlspecialchars($room->nama_ruangan) ?>
        </h2>
        <div class="flex flex-wrap gap-4 text-slate-600">
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span><span class="font-semibold">
                <?= (int) $room->kapasitas_min ?> -
                <?= (int) $room->kapasitas_max ?>
              </span>
              orang</span>
          </div>
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span>
              <?= htmlspecialchars($room->jenis_ruangan) ?>
            </span>
          </div>
        </div>
      </div>
      <div>
        <?php
        $statusColors = [
          'tersedia' => 'bg-green-100 text-green-800 border-green-300',
          'tidak tersedia' => 'bg-red-100 text-red-800 border-red-300',
          'maintenance' => 'bg-yellow-100 text-yellow-800 border-yellow-300'
        ];
        $statusColor = $statusColors[strtolower($room->status_ruangan)] ?? 'bg-gray-100 text-gray-800 border-gray-300';
        ?>
        <span class="inline-block px-4 py-2 rounded-lg font-semibold text-sm border-2 <?= $statusColor ?>">
          <?= htmlspecialchars(ucfirst($room->status_ruangan)) ?>
        </span>
      </div>
    </div>

    <!-- Photos -->
    <?php if (!empty($photos)): ?>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <?php foreach ($photos as $photo): ?>
          <img src="<?= $photo ?>" alt="Foto 
      <?= htmlspecialchars($room->nama_ruangan) ?>"
            class="w-full h-40 object-cover rounded-xl shadow-md hover:shadow-lg transition-shadow cursor-pointer">
        <?php endforeach; ?>
      </div> <?php endif; ?>

    <!-- Facilities -->
    <?php if (!empty($facilities)): ?>
      <div class="border-t pt-6">
        <h3 class="text-xl font-semibold text-slate-800 mb-4 flex items-center">
          <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Fasilitas
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <?php foreach ($facilities as $facility): ?>
            <div class="flex items-center text-slate-700 bg-slate-50 px-4 py-2 rounded-lg">
              <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
              <?= htmlspecialchars($facility) ?>
            </div> <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
  <!-- Availability Calendar -->
  <?php if (!empty($availability)): ?>
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
      <h3 class="text-xl font-semibold text-slate-800 mb-4 flex items-center">
        <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        Kalender Ketersediaan (7 Hari Ke Depan, excluding weekend);
      </h3>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="px-4 py-3 text-left font-semibold text-slate-700 rounded-tl-lg">Tanggal</th>
              <th class="px-4 py-3 text-left font-semibold text-slate-700 rounded-tr-lg">Status Ketersediaan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            <?php
            $availabilityRowColors = [
              'available' => 'bg-green-50 hover:bg-green-100',
              'blocked' => 'bg-red-50 hover:bg-red-100',
            ];

            foreach ($availability as $day):
              $rowColor = $availabilityRowColors[$day['availability_status']] ?? 'hover:bg-slate-50';
              ?>
              <tr class="transition-colors <?= $rowColor ?>">
                <td class="px-4 py-4 font-medium text-slate-700">
                  <?= htmlspecialchars($day['day_short']) ?>, <?= htmlspecialchars($day['day_number']) ?>
                  <?= htmlspecialchars($day['month']) ?>
                </td>
                <td class="px-4 py-4">
                  <?php if ($day['is_blocked']): ?>
                    <!-- Blocked Date -->
                    <div class="space-y-2">
                      <div class="flex items-center gap-2 text-red-800 font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Blocked
                      </div>
                      <div class="text-sm text-slate-600 italic">
                        Reason: <?= htmlspecialchars($day['blocking_reason']) ?>
                      </div>
                    </div>
                  <?php elseif (empty($day['bookings'])): ?>
                    <!-- Available -->
                    <div class="flex items-center gap-2 text-green-800 font-semibold">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      Available
                    </div>
                    <!-- Show existing bookings if any -->
                  <?php elseif (!empty($day['bookings'])): ?>
                    <div class="space-y-1 mt-2">
                      <div class="text-xs text-slate-500">Booked slots:</div>
                      <?php foreach ($day['bookings'] as $booking): ?>
                        <div class="text-sm text-slate-600">
                          <?= htmlspecialchars(substr($booking['waktu_mulai'], 0, 5)) ?> -
                          <?= htmlspecialchars(substr($booking['waktu_selesai'], 0, 5)) ?>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>

  <!-- Booking Form -->
  <div class="bg-white rounded-2xl shadow-lg p-8">
    <h3 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
      <svg class="w-7 h-7 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
      </svg>
      Buat Booking
    </h3>

    <?php if (auth()->user()->status === 'pending kubaca' || auth()->user()->status === 'rejected'): ?>
      <!-- Blocked Booking Form Overlay -->
      <div class="relative">
        <!-- Blurred Form (for visual context) -->
        <div class="pointer-events-none select-none blur-sm opacity-40">
          <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Penggunaan</label>
                <input type="date" disabled class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl">
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Waktu Mulai</label>
                <input type="time" disabled class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl">
              </div>
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Waktu Selesai</label>
                <input type="time" disabled class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl">
              </div>
            </div>
            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Tujuan Penggunaan</label>
              <select disabled class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl">
                <option>Pilih tujuan penggunaan</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Overlay Message -->
        <div class="absolute inset-0 flex items-center justify-center">
          <div
            class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border-2 <?= auth()->user()->status === 'pending kubaca' ? 'border-amber-300' : 'border-red-300' ?> p-8 max-w-md text-center transform hover:scale-105 transition-transform">
            <div
              class="w-20 h-20 mx-auto mb-4 <?= auth()->user()->status === 'pending kubaca' ? 'bg-amber-100' : 'bg-red-100' ?> rounded-full flex items-center justify-center">
              <?php if (auth()->user()->status === 'pending kubaca'): ?>
                <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
              <?php else: ?>
                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              <?php endif; ?>
            </div> <?php if (auth()->user()->status === 'pending kubaca'): ?>
              <h4 class="text-xl font-bold text-amber-900 mb-3">Booking Tidak Tersedia</h4>
              <p class="text-amber-800 mb-2 leading-relaxed">
                Akun Anda sedang dalam proses verifikasi. Anda tidak dapat membuat booking hingga akun Anda disetujui
                oleh
                admin.
              </p>
              <p class="text-sm text-amber-700">
                Harap menunggu konfirmasi dari admin.
              </p>
            <?php else: ?>
              <h4 class="text-xl font-bold text-red-900 mb-3">Akses Ditolak</h4>
              <p class="text-red-800 mb-2 leading-relaxed">
                Akun Anda telah ditolak. Anda tidak dapat membuat booking saat ini.
              </p>
              <p class="text-sm text-red-700">
                Silakan upload kembali kubaca di profile.
              </p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php else: ?>
      <!-- Active Booking Form -->
      <form action="/bookings/draft" method="post" enctype="multipart/form-data" class="space-y-6">
        <?= csrf_field() ?>
        <input type="hidden" name="ruangan_id" value="
                <?= (int) $room->id_ruangan ?>">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Penggunaan</label>
            <input type="date" name="tanggal_penggunaan_ruang" value="<?= old('tanggal_penggunaan_ruang') ?>" required
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Waktu Mulai</label>
            <input type="time" name="waktu_mulai" value="<?= old('waktu_mulai') ?>" required
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Waktu Selesai</label>
            <input type="time" name="waktu_selesai" value="<?= old('waktu_selesai') ?>" required
              class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
          </div>
        </div>

        <div>
          <?php
          $tujuanMahasiswa = [
            'Diskusi Kelompok',
            'Belajar Bersama',
            'Presentasi Tugas',
            'Pembuatan Konten Edukasi',
            'Rekaman Audio/Video',
            'Konseling Akademik',
            'Latihan Bahasa Asing',
            'Kegiatan Literasi',
            'Proyek Kreatif'
          ];
          ?>
          <label for="tujuan" class="block text-sm font-semibold text-slate-700 mb-2">Tujuan Penggunaan</label>
          <select name="tujuan" id="tujuan" required
            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
            <option value="" disabled selected>Pilih tujuan penggunaan</option>
            <?php foreach ($tujuanMahasiswa as $tujuan): ?>
              <option value="<?= htmlspecialchars($tujuan) ?>" <?= old('tujuan') == $tujuan ? 'selected' : '' ?>>
                <?= htmlspecialchars($tujuan) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php if (auth()->user()->isDosen() || auth()->user()->isTendik()): ?>
          <div class="border-t pt-6 space-y-6">
            <h4 class="font-semibold text-slate-800 text-lg">Informasi Tambahan (Dosen/Pegawai)</h4>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Alasan Pemakaian</label>
              <select name="pegawai_reason" required
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                <option value="">Pilih alasan</option>
                <option value="rapat">Rapat</option>
                <option value="seminar">Seminar</option>
                <option value="presentasi">Presentasi</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-semibold text-slate-700 mb-2">Unggah Surat Tugas / Undangan</label>
              <input type="file" name="pegawai_file" accept=".pdf,.jpg,.png" required
                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
              <p class="mt-2 text-sm text-slate-500">Format yang diterima: PDF, JPG, PNG</p>
            </div>
          </div>
        <?php endif; ?>

        <div class="flex gap-3 pt-4">
          <button type="submit"
            class="flex-1 bg-primary text-white px-8 py-4 rounded-xl hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Buat Draft Booking
          </button>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>