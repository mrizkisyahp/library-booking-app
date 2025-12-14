<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-900">System Settings</h2>
    <p class="text-gray-600 mt-2">Konfigurasi operasional sistem booking perpustakaan</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Library Status Card -->
    <div
        class="bg-white rounded-2xl shadow-lg p-6 border-2 <?= $libraryClosedToday ? 'border-red-300' : 'border-emerald-300' ?>">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Status Perpustakaan</h3>
                <p class="text-sm text-gray-500">Status operasional saat ini</p>
            </div>
            <a href="/admin/blocked-dates"
                class="text-emerald-600 hover:text-emerald-700 font-medium text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Kelola Blokir
            </a>
        </div>
        <div class="space-y-4">
            <?php if ($libraryClosedToday): ?>
                <!-- CLOSED Status -->
                <div class="flex items-start p-4 bg-red-50 rounded-xl border border-red-200">
                    <svg class="w-8 h-8 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <div class="ml-4">
                        <p class="font-bold text-red-900 text-lg">TUTUP</p>
                        <p class="text-sm text-red-800 mt-1">Perpustakaan sedang ditutup sementara</p>
                        <?php if ($closureReason): ?>
                            <div class="mt-3 p-2 bg-white rounded border border-red-200">
                                <p class="text-xs font-medium text-gray-700">Alasan:</p>
                                <p class="text-sm text-gray-900"><?= htmlspecialchars($closureReason) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- OPEN Status -->
                <div class="flex items-start p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                    <svg class="w-8 h-8 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="ml-4">
                        <p class="font-bold text-emerald-900 text-lg">BUKA</p>
                        <p class="text-sm text-emerald-800 mt-1">Perpustakaan beroperasi normal</p>
                        <p class="text-xs text-gray-600 mt-2">Pengguna dapat membuat booking</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Operating Hours & Sessions -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Jam Operasional</h3>
                <p class="text-sm text-gray-500">Jadwal perpustakaan dan sesi booking</p>
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Jam Perpustakaan</label>
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg mb-3">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-900">Buka - Tutup</span>
                        <span class="text-emerald-700 font-bold">08:00 - 16:20</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div class="p-2 bg-slate-50 rounded-lg text-center">
                        <p class="text-xs text-gray-500">Buffer Awal</p>
                        <p class="font-semibold text-gray-900">15 menit</p>
                    </div>
                    <div class="p-2 bg-slate-50 rounded-lg text-center">
                        <p class="text-xs text-gray-500">Buffer Akhir</p>
                        <p class="font-semibold text-gray-900">20 menit</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500">Booking tersedia 08:15 - 16:00 (dengan buffer)</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Waktu Booking</label>
                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium text-blue-800">Free-form Booking</span>
                    </div>
                    <p class="text-sm text-blue-700">
                        User dapat memilih waktu booking secara bebas dalam jam operasional,
                        selama tidak melewati jam istirahat.
                    </p>
                    <div class="mt-3 p-2 bg-white rounded-lg">
                        <p class="text-xs text-gray-600">
                            <strong>Contoh valid:</strong> 08:30-10:30, 09:00-10:45, 12:30-14:00 (Sen-Kam), 13:30-15:00
                        </p>
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
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-sm font-medium text-gray-700">No-Show Grace Period</p>
                    <p class="text-xs text-gray-500">Toleransi telat check-in</p>
                </div>
                <span class="text-lg font-bold text-gray-900">10 menit</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-sm font-medium text-gray-700">Reschedule Limit</p>
                    <p class="text-xs text-gray-500">Maksimal reschedule per booking</p>
                </div>
                <span class="text-lg font-bold text-gray-900">1 kali</span>
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
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-sm font-medium text-gray-700">Max Pending Invitations</p>
                    <p class="text-xs text-gray-500">Undangan pending per user</p>
                </div>
                <span class="text-lg font-bold text-gray-900">3</span>
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