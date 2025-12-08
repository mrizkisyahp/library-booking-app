<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-900">System Settings</h2>
    <p class="text-gray-600 mt-2">Konfigurasi operasional sistem booking perpustakaan</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Operating Hours & Sessions -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Jam Operasional</h3>
                <p class="text-sm text-gray-500">Sesi booking yang tersedia</p>
            </div>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hari Operasional</label>
                <div class="flex flex-wrap gap-2">
                    <?php
                    $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                    $activeDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                    foreach ($days as $day):
                        $isActive = in_array($day, $activeDays);
                        ?>
                        <span
                            class="px-3 py-1 text-sm rounded-full <?= $isActive ? 'bg-emerald-100 text-emerald-700 font-medium' : 'bg-gray-100 text-gray-400' ?>">
                            <?= $day ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Sesi Booking</label>
                <div class="space-y-2">
                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-900">Sesi 1</span>
                            <span class="text-blue-700 font-semibold">08:15 - 10:55</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">Pagi (2 jam 40 menit)</p>
                    </div>
                    <div class="p-3 bg-purple-50 border border-purple-200 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-medium text-gray-900">Sesi 2</span>
                            <span class="text-purple-700 font-semibold">13:15 - 16:00</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">Siang (2 jam 45 menit)</p>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jam Istirahat</label>
                <div class="space-y-2">
                    <div class="p-2 bg-gray-50 rounded-lg text-sm">
                        <strong>Senin - Kamis:</strong> 11:00 - 12:00
                    </div>
                    <div class="p-2 bg-gray-50 rounded-lg text-sm">
                        <strong>Jumat:</strong> 11:00 - 13:00 (Istirahat Jumat)
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Booking tidak boleh melewati jam istirahat</p>
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
                <span class="text-lg font-bold text-gray-900">1 jam</span>
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
                    <p class="text-sm font-medium text-gray-700">Minimal Lead Time</p>
                    <p class="text-xs text-gray-500">Booking harus dibuat minimal sebelum</p>
                </div>
                <span class="text-lg font-bold text-gray-900">15 menit</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-sm font-medium text-gray-700">Advance Booking</p>
                    <p class="text-xs text-gray-500">Bisa booking sampai berapa hari ke depan</p>
                </div>
                <span class="text-lg font-bold text-gray-900">7 hari kerja</span>
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
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                <p class="text-xs text-amber-800">
                    <strong>Catatan:</strong> Peringatan otomatis diberikan saat user tidak hadir (No-Show). Setelah 3
                    peringatan, akun akan otomatis di-suspend selama 7 hari.
                </p>
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
                    <span
                        class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-medium">Aktif</span>
                </div>
                <p class="text-xs text-gray-500 mt-2">Berjalan setiap hari jam 00:01 untuk:</p>
                <ul class="text-xs text-gray-600 mt-1 ml-4 list-disc">
                    <li>Menonaktifkan user dengan <code>masa_aktif</code> expired</li>
                    <li>Membersihkan booking kadaluarsa</li>
                </ul>
            </div>
            <div class="p-4 border border-gray-200 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <p class="font-medium text-gray-900">Queue Worker</p>
                        <p class="text-xs text-gray-600">Email dan notifikasi otomatis</p>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Running</span>
                </div>
                <p class="text-xs text-gray-500 mt-2">Proses pengiriman email dan notifikasi berjalan secara
                    asynchronous</p>
            </div>
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800">
                    <strong>Info:</strong> Semua nilai di halaman ini sudah dikonfigurasi langsung di kode program.
                    Untuk mengubahnya, perlu edit file <code>BookingServices.php</code> dan
                    <code>UserServices.php</code>.
                </p>
            </div>
        </div>
    </div>
</div>