<?php
use App\Core\App;

// Extract settings values with defaults
$getValue = function($key, $default = '') use ($settings) {
    return $settings[$key]['value'] ?? $default;
};
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">System Settings</h1>
        <p class="text-gray-600 mt-2">Konfigurasi operasional sistem booking perpustakaan</p>
    </div>

    <?php if ($message = App::$app->session->getFlash('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($message = App::$app->session->getFlash('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form action="/admin/settings/update" method="post">
        <?= csrf_field() ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Library Status Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border-2 <?= $libraryClosedToday ? 'border-red-300' : 'border-emerald-300' ?>">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Status Perpustakaan</h3>
                        <p class="text-sm text-gray-500">Status operasional saat ini</p>
                    </div>
                    <a href="/admin/blocked-dates"
                        class="text-emerald-600 hover:text-emerald-700 font-medium text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Kelola Blokir
                    </a>
                </div>
                <div class="space-y-4">
                    <?php if ($libraryClosedToday): ?>
                        <div class="flex items-start p-4 bg-red-50 rounded-xl border border-red-200">
                            <svg class="w-8 h-8 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
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
                        <div class="flex items-start p-4 bg-emerald-50 rounded-xl border border-emerald-200">
                            <svg class="w-8 h-8 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="ml-4">
                                <p class="font-bold text-emerald-900 text-lg">BUKA</p>
                                <p class="text-sm text-emerald-800 mt-1">Perpustakaan beroperasi normal</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Operating Days -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Hari Operasional</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <?php
                    $days = [
                        'monday' => 'Senin',
                        'tuesday' => 'Selasa',
                        'wednesday' => 'Rabu',
                        'thursday' => 'Kamis',
                        'friday' => 'Jumat',
                        'saturday' => 'Sabtu',
                        'sunday' => 'Minggu',
                    ];
                    foreach ($days as $key => $label):
                        $isActive = $getValue("operating_day_{$key}", false);
                    ?>
                        <label class="flex items-center p-3 border-2 rounded-xl cursor-pointer transition-all hover:bg-gray-50 <?= $isActive ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200' ?>">
                            <input type="checkbox" name="operating_day_<?= $key ?>" value="1" <?= $isActive ? 'checked' : '' ?>
                                class="w-4 h-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <span class="ml-2 text-sm font-medium text-gray-700"><?= $label ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Operating Hours -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Jam Operasional</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jam Buka</label>
                        <input type="time" name="library_open_time" value="<?= htmlspecialchars($getValue('library_open_time', '08:00')) ?>"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jam Tutup</label>
                        <input type="time" name="library_close_time" value="<?= htmlspecialchars($getValue('library_close_time', '16:20')) ?>"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                    </div>
                </div>
            </div>

            <!-- Break Times -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Jam Istirahat</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Senin - Kamis</p>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="time" name="break_start_weekday" value="<?= htmlspecialchars($getValue('break_start_weekday', '11:00')) ?>"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                            <input type="time" name="break_end_weekday" value="<?= htmlspecialchars($getValue('break_end_weekday', '12:00')) ?>"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-700 mb-2">Jumat</p>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="time" name="break_start_friday" value="<?= htmlspecialchars($getValue('break_start_friday', '11:00')) ?>"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                            <input type="time" name="break_end_friday" value="<?= htmlspecialchars($getValue('break_end_friday', '13:00')) ?>"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Duration -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 lg:col-span-2">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Durasi Booking</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Durasi Minimal (menit)</label>
                        <input type="number" name="min_booking_duration" value="<?= (int) $getValue('min_booking_duration', 60) ?>" min="1" max="480" step="1"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                        <p class="text-xs text-gray-500 mt-1">Minimal 1 menit</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Durasi Maksimal (menit)</label>
                        <input type="number" name="max_booking_duration" value="<?= (int) $getValue('max_booking_duration', 180) ?>" min="1" max="480" step="1"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                        <p class="text-xs text-gray-500 mt-1">Maksimal 480 menit (8 jam)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end mb-8">
            <button type="submit"
                class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simpan Pengaturan
            </button>
        </div>
    </form>

    <!-- Blocked Dates Section -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-xl font-semibold text-gray-900">Tanggal Diblokir</h3>
                <p class="text-sm text-gray-500">Kelola tanggal yang tidak tersedia untuk booking</p>
            </div>
            <a href="/admin/blocked-dates"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Blokir
            </a>
        </div>

        <?php if (empty($blockedDates)): ?>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-gray-600">Tidak ada tanggal yang diblokir</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Periode</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Ruangan</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Alasan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($blockedDates, 0, 5) as $blocked): ?>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-4 px-4">
                                    <div class="font-semibold text-gray-800">
                                        <?= date('d M Y', strtotime($blocked['tanggal_begin'])) ?>
                                    </div>
                                    <?php if ($blocked['tanggal_begin'] !== $blocked['tanggal_end']): ?>
                                        <div class="text-sm text-gray-500">
                                            s/d <?= date('d M Y', strtotime($blocked['tanggal_end'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4">
                                    <?php if (empty($blocked['ruangan_id'])): ?>
                                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Semua Ruangan
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-800"><?= htmlspecialchars($blocked['nama_ruangan'] ?? 'Unknown') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="text-gray-600"><?= htmlspecialchars($blocked['alasan'] ?? '-') ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (count($blockedDates) > 5): ?>
                <div class="mt-4 text-center">
                    <a href="/admin/blocked-dates" class="text-emerald-600 hover:text-emerald-700 font-medium">
                        Lihat semua (<?= count($blockedDates) ?> total) →
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>