<!-- Back Button -->
<div class="p-4 bg-white shadow-md w-full">
    <div class="flex items-center gap-4 py-4">
        <div class="flex items-center gap-4 ">
            <a href="/rooms">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-chevron-left-icon lucide-chevron-left size-9">
                    <path d="m15 18-6-6 6-6" />
                </svg>
            </a>
            <span class="text-black font-bold text-4xl">
                Detail Ruangan
            </span>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto">
    <!-- Room thumbnail -->
    <div class="my-6">
        <div class="grid grid-cols-4 gap-4 mb-4">
            <?php if (!empty($photos)): ?>
                <?php foreach ($photos as $photo): ?>
                    <img src="<?= $photo ?>" alt="Foto
                             <?= htmlspecialchars($room->nama_ruangan) ?>"
                        class="object-cover h-full rounded-3xl shadow-md hover:shadow-lg transition-shadow cursor-pointer">
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- room content -->
    <div class="bg-white rounded-3xl p-6 mb-6">
        <div class="font-bold text-4xl mb-2 flex items-center justify-between">
            <?= htmlspecialchars($room->nama_ruangan) ?>
            <?php
            $statusColors = [
                'tersedia' => 'bg-green-100 text-green-800 border-green-300',
                'tidak tersedia' => 'bg-red-100 text-red-800 border-red-300',
                'maintenance' => 'bg-yellow-100 text-yellow-800 border-yellow-300'
            ];
            $statusColor = $statusColors[strtolower($room->status_ruangan)] ?? 'bg-gray-100 text-gray-800 border-gray-300';
            ?>
            <div class="flex items-center gap-2">
                <span class="inline-block px-4 py-2 rounded-lg font-semibold text-sm border-2 <?= $statusColor ?>">
                    <?= htmlspecialchars(ucfirst($room->status_ruangan)) ?>
                </span>
                <?php if ($room->requires_special_approval): ?>
                    <span
                        class="inline-block px-3 py-2 rounded-lg font-semibold text-sm bg-blue-100 text-blue-700 border-2 border-blue-300">
                        <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Butuh Dokumen
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="text-gray-400 mb-4">
            <?= htmlspecialchars($room->jenis_ruangan) ?>
        </div>
        <div class="flex items-center gap-4 mb-6">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-users-round-icon lucide-users-round size-4">
                    <path d="M18 21a8 8 0 0 0-16 0" />
                    <circle cx="10" cy="8" r="5" />
                    <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3" />
                </svg>

                <?= (int) $room->kapasitas_min ?> —
                <?= (int) $room->kapasitas_max ?>
            </div>
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-star-icon lucide-star size-4">
                    <path
                        d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z" />
                </svg>
                <?= $room->avg_rating ? number_format($room->avg_rating, 1) : '-' ?>
            </div>
        </div>
        <div class="mb-6">
            <h1 class="font-bold text-2xl mb-4">
                Deskripsi
            </h1>
            <!-- fasilitas/deskripsi ruangan di sini -->
            <?php if (!empty($facilities)): ?>
                <div class="border-t pt-6">
                    <h3 class="text-xl font-semibold text-slate-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Fasilitas
                    </h3>
                    <?php foreach ($facilities as $facility): ?>
                        <div class="flex items-center text-slate-700 bg-slate-50 px-4 py-2 rounded-lg">
                            <svg class="w-4 h-4 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <?= htmlspecialchars($facility) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Availability Calendar -->
<?php if (!empty($availability)): ?>
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-6 max-w-7xl mx-auto">
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
                        <th class="px-4 py-3 text-left font-semibold text-slate-700 rounded-tr-lg">Status Ketersediaan
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($availability as $day): ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-4 font-medium text-slate-700">
                                <?= htmlspecialchars($day['day_short']) ?>, <?= htmlspecialchars($day['day_number']) ?>
                                <?= htmlspecialchars($day['month']) ?>
                            </td>
                            <td class="px-4 py-4">
                                <?php if (empty($day['bookings'])): ?>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Tersedia Sepanjang Hari
                                    </span>
                                <?php else: ?>
                                    <div class="space-y-2">
                                        <?php foreach ($day['bookings'] as $booking): ?>
                                            <?php
                                            $slotStatusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'verified' => 'bg-blue-100 text-blue-800',
                                                'active' => 'bg-red-100 text-red-800',
                                                'completed' => 'bg-gray-100 text-gray-800'
                                            ];
                                            $slotColor = $slotStatusColors[strtolower($booking['status_booking'])] ?? 'bg-gray-100 text-gray-800';
                                            ?>
                                            <div class="flex items-center gap-2">
                                                <span class="text-slate-600 font-medium">
                                                    <?= htmlspecialchars(substr($booking['waktu_mulai'], 0, 5)) ?> -
                                                    <?= htmlspecialchars(substr($booking['waktu_selesai'], 0, 5)) ?>
                                                </span>
                                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $slotColor ?>">
                                                    <?= htmlspecialchars(ucfirst($booking['status_booking'])) ?>
                                                </span>
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
<div class="bg-white rounded-2xl shadow-lg p-8 max-w-7xl mx-auto">
    <h3 class="text-2xl font-bold text-slate-800 mb-6 flex items-center">
        <svg class="w-7 h-7 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
        Buat Booking
    </h3>

    <?php
    $userStatus = auth()->user()->status;
    $isBlocked = $userStatus === 'pending kubaca' || $userStatus === 'rejected';
    $libraryClosedToday = !auth()->user()->isAdmin() && isLibraryEffectivelyClosed();
    $shouldShowOverlay = $isBlocked || $libraryClosedToday;
    ?>

    <?php if ($shouldShowOverlay): ?>
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
                <?php if ($libraryClosedToday): ?>
                    <!-- Library Closed Overlay -->
                    <?php $closureReason = getClosureReason(date('Y-m-d')); ?>
                    <div
                        class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border-2 border-blue-300 p-8 max-w-md text-center transform hover:scale-105 transition-transform">
                        <div class="w-20 h-20 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-blue-900 mb-3">Perpustakaan Sedang Tutup</h4>
                        <p class="text-blue-800 mb-2 leading-relaxed">
                            Perpustakaan sedang ditutup sementara. Anda dapat melihat ruangan tetapi tidak dapat membuat
                            booking.
                        </p>
                        <?php if ($closureReason): ?>
                            <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <p class="text-sm font-semibold text-blue-900 mb-1">Alasan:</p>
                                <p class="text-sm text-blue-800"><?= htmlspecialchars($closureReason) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php elseif ($userStatus === 'pending kubaca'): ?>
                    <!-- Pending Kubaca Overlay -->
                    <div
                        class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border-2 border-amber-300 p-8 max-w-md text-center transform hover:scale-105 transition-transform">
                        <div class="w-20 h-20 mx-auto mb-4 bg-amber-100 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-amber-900 mb-3">Booking Tidak Tersedia</h4>
                        <p class="text-amber-800 mb-2 leading-relaxed">
                            Akun Anda sedang dalam proses verifikasi. Anda tidak dapat membuat booking hingga akun Anda
                            disetujui
                            oleh
                            admin.
                        </p>
                        <p class="text-sm text-amber-700">
                            Harap menunggu konfirmasi dari admin.
                        </p>
                    </div>
                <?php else:  // rejected ?>
                    <!-- Rejected Overlay -->
                    <div
                        class="bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border-2 border-red-300 p-8 max-w-md text-center transform hover:scale-105 transition-transform">
                        <div class="w-20 h-20 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-red-900 mb-3">Akses Ditolak</h4>
                        <p class="text-red-800 mb-2 leading-relaxed">
                            Akun Anda telah ditolak. Anda tidak dapat membuat booking saat ini.
                        </p>
                        <p class="text-sm text-red-700">
                            Silakan upload kembali kubaca di profile.
                        </p>
                    </div>
                <?php endif; ?>
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
                    <input type="date" name="tanggal_penggunaan_ruang" value="<?= old('tanggal_penggunaan_ruang') ?>"
                        required
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
            <?php if ($room->requires_special_approval && (auth()->user()->isDosen() || auth()->user()->isTendik())): ?>
                <div class="border-t pt-6 space-y-6">
                    <h4 class="font-semibold text-slate-800 text-lg">Informasi Tambahan (Dokumen Diperlukan)</h4>

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
                        <p class="mt-2 text-sm text-slate-500">Format yang diterima: PDF, JPG, PNG (Max 2MB)</p>
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