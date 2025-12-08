<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-900">System Settings</h2>
  <p class="text-gray-600 mt-2">Konfigurasi operasional sistem booking perpustakaan</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <!-- Operating Hours -->
  <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-xl font-semibold text-gray-900">Jam Operasional</h3>
        <p class="text-sm text-gray-500">Waktu buka dan tutup perpustakaan</p>
      </div>
    </div>
    <div class="space-y-4">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Jam Buka</label>
          <input type="time" value="12:00" disabled
            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
          <p class="text-xs text-gray-500 mt-1">12:00</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Jam Tutup</label>
          <input type="time" value="18:00" disabled
            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
          <p class="text-xs text-gray-500 mt-1">18:00</p>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Hari Operasional</label>
        <div class="flex flex-wrap gap-2">
          <?php
          $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
          $activeDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
          foreach ($days as $day):
              $isActive = in_array($day, $activeDays);
              ?>
                <span class="px-3 py-1 text-sm rounded-full <?= $isActive ? 'bg-emerald-100 text-emerald-700 font-medium' : 'bg-gray-100 text-gray-400' ?>">
                  <?= $day ?>
                </span>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Booking Rules -->
  <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-xl font-semibold text-gray-900">Aturan Booking</h3>
        <p class="text-sm text-gray-500">Batasan waktu dan jumlah booking</p>
      </div>
    </div>
    <div class="space-y-3">
      <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
        <div>
          <p class="text-sm font-medium text-gray-700">Durasi Minimal</p>
          <p class="text-xs text-gray-500">Lama booking paling singkat</p>
        </div>
        <span class="text-lg font-bold text-gray-900">30 menit</span>
      </div>
      <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
        <div>
          <p class="text-sm font-medium text-gray-700">Durasi Maksimal</p>
          <p class="text-xs text-gray-500">Lama booking paling lama</p>
        </div>
        <span class="text-lg font-bold text-gray-900">3 jam</span>
      </div>
      <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
        <div>
          <p class="text-sm font-medium text-gray-700">Booking Per Hari</p>
          <p class="text-xs text-gray-500">Maksimal booking per user per hari</p>
        </div>
        <span class="text-lg font-bold text-gray-900">1 kali</span>
      </div>
      <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
        <div>
          <p class="text-sm font-medium text-gray-700">Draft Expiry</p>
          <p class="text-xs text-gray-500">Draft otomatis dihapus setelah</p>
        </div>
        <span class="text-lg font-bold text-gray-900">30 menit</span>
      </div>
      <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
        <div>
          <p class="text-sm font-medium text-gray-700">Advance Booking</p>
          <p class="text-xs text-gray-500">Bisa booking sampai berapa hari ke depan</p>
        </div>
        <span class="text-lg font-bold text-gray-900">7 hari</span>
      </div>
    </div>
  </div>

  <!-- User Management -->
  <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-xl font-semibold text-gray-900">Manajemen User</h3>
        <p class="text-sm text-gray-500">Peringatan dan sanksi pengguna</p>
      </div>
    </div>
    <div class="space-y-3">
      <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
        <div>
          <p class="text-sm font-medium text-gray-700">Maksimal Peringatan</p>
          <p class="text-xs text-gray-500">Peringatan sebelum akun di-suspend</p>
        </div>
        <span class="text-lg font-bold text-red-600">3 kali</span>
      </div>
      <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
        <div>
          <p class="text-sm font-medium text-gray-700">Durasi Suspensi</p>
          <p class="text-xs text-gray-500">Lama akun tidak bisa digunakan</p>
        </div>
        <span class="text-lg font-bold text-orange-600">7 hari</span>
      </div>
      <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
        <div>
          <p class="text-sm font-medium text-gray-700">Masa Aktif Default</p>
          <p class="text-xs text-gray-500">Lama akun aktif setelah approval</p>
        </div>
        <span class="text-lg font-bold text-emerald-600">365 hari</span>
      </div>
    </div>
  </div>

  <!-- System -->
  <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-xl font-semibold text-gray-900">Status Sistem</h3>
        <p class="text-sm text-gray-500">Proses otomatis yang berjalan</p>
      </div>
    </div>
    <div class="space-y-3">
      <div class="p-4 border border-gray-200 rounded-lg">
        <div class="flex items-center justify-between mb-2">
          <div>
            <p class="font-medium text-gray-900">Scheduler</p>
            <p class="text-xs text-gray-600">Pembersihan data otomatis setiap hari</p>
          </div>
          <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">Aktif</span>
        </div>
        <p class="text-xs text-gray-500 mt-2">Berjalan setiap hari jam 00:01 untuk menghapus booking kadaluarsa dan akun expired</p>
      </div>
      <div class="p-4 border border-gray-200 rounded-lg">
        <div class="flex items-center justify-between mb-2">
          <div>
            <p class="font-medium text-gray-900">Queue Worker</p>
            <p class="text-xs text-gray-600">Email dan notifikasi otomatis</p>
          </div>
          <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Running</span>
        </div>
        <p class="text-xs text-gray-500 mt-2">Proses pengiriman email dan notifikasi berjalan secara asynchronous</p>
      </div>
      <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
        <p class="text-sm text-amber-800">
          <strong>Catatan:</strong> Pengaturan ini sudah dikonfigurasi secara otomatis. Untuk mengubah nilai, silakan hubungi developer atau edit konfigurasi di code.
        </p>
      </div>
    </div>
  </div>
</div>