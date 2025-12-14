<?php
use App\Core\App;
$validator = $validator ?? null;
?>

<div class="container mx-auto px-4 py-6 max-w-2xl">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="/admin/bookings/detail?id=<?= (int) $booking->id_booking ?>"
            class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold group">
            <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Detail Booking
        </a>
    </div>

    <?php if ($message = App::$app->session->getFlash('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
        <h2 class="text-3xl font-bold text-slate-800 flex items-center mb-2">
            <svg class="w-8 h-8 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Reschedule Booking (Admin)
        </h2>
        <p class="text-slate-600">Ubah jadwal booking ini</p>
    </div>

    <!-- Current Booking Info -->
    <div class="bg-amber-50 rounded-2xl shadow-lg p-6 mb-6 border-2 border-amber-200">
        <h3 class="font-bold text-amber-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Jadwal Saat Ini
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="bg-white rounded-lg p-3">
                <p class="text-amber-600 font-semibold">Ruangan</p>
                <p class="text-slate-800 font-bold"><?= htmlspecialchars($booking->nama_ruangan ?? 'Unknown') ?></p>
            </div>
            <div class="bg-white rounded-lg p-3">
                <p class="text-amber-600 font-semibold">Tanggal</p>
                <p class="text-slate-800 font-bold"><?= date('d M Y', strtotime($booking->tanggal_penggunaan_ruang)) ?>
                </p>
            </div>
            <div class="bg-white rounded-lg p-3">
                <p class="text-amber-600 font-semibold">Waktu</p>
                <p class="text-slate-800 font-bold">
                    <?= htmlspecialchars(substr($booking->waktu_mulai, 0, 5)) ?> -
                    <?= htmlspecialchars(substr($booking->waktu_selesai, 0, 5)) ?>
                </p>
            </div>
        </div>
        <div class="mt-4 bg-white rounded-lg p-3">
            <p class="text-amber-600 font-semibold text-sm">Booking Code</p>
            <p class="text-slate-800 font-bold font-mono"><?= htmlspecialchars($booking->checkin_code ?? '-') ?></p>
        </div>
    </div>

    <!-- Reschedule Form -->
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Jadwal Baru
        </h3>

        <form action="/admin/bookings/reschedule" method="post" class="space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">

            <!-- Date -->
            <div>
                <label
                    class="block text-sm font-semibold mb-2 <?= $validator?->hasError('tanggal_penggunaan_ruang') ? 'text-red-700' : 'text-slate-700' ?>">Tanggal
                    Penggunaan</label>
                <input type="date" name="tanggal_penggunaan_ruang" required min="<?= date('Y-m-d') ?>"
                    value="<?= htmlspecialchars(old('tanggal_penggunaan_ruang') ?? $booking->tanggal_penggunaan_ruang) ?>"
                    class="w-full px-4 py-3 border-2 rounded-xl focus:outline-none focus:ring-2 transition-all <?= $validator?->hasError('tanggal_penggunaan_ruang') ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-emerald-500 focus:ring-emerald-200' ?>">
                <?php if ($validator?->hasError('tanggal_penggunaan_ruang')): ?>
                    <p class="mt-1 text-sm text-red-600">
                        <?= htmlspecialchars($validator->getFirstError('tanggal_penggunaan_ruang')) ?></p>
                <?php endif; ?>
            </div>

            <!-- Start Time -->
            <div>
                <label
                    class="block text-sm font-semibold mb-2 <?= $validator?->hasError('waktu_mulai') ? 'text-red-700' : 'text-slate-700' ?>">Waktu
                    Mulai</label>
                <input type="time" name="waktu_mulai" required min="07:00" max="16:00"
                    value="<?= htmlspecialchars(old('waktu_mulai') ?? substr($booking->waktu_mulai, 0, 5)) ?>"
                    class="w-full px-4 py-3 border-2 rounded-xl focus:outline-none focus:ring-2 transition-all <?= $validator?->hasError('waktu_mulai') ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-emerald-500 focus:ring-emerald-200' ?>">
                <?php if ($validator?->hasError('waktu_mulai')): ?>
                    <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($validator->getFirstError('waktu_mulai')) ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- End Time -->
            <div>
                <label
                    class="block text-sm font-semibold mb-2 <?= $validator?->hasError('waktu_selesai') ? 'text-red-700' : 'text-slate-700' ?>">Waktu
                    Selesai</label>
                <input type="time" name="waktu_selesai" required min="08:00" max="17:00"
                    value="<?= htmlspecialchars(old('waktu_selesai') ?? substr($booking->waktu_selesai, 0, 5)) ?>"
                    class="w-full px-4 py-3 border-2 rounded-xl focus:outline-none focus:ring-2 transition-all <?= $validator?->hasError('waktu_selesai') ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-emerald-500 focus:ring-emerald-200' ?>">
                <?php if ($validator?->hasError('waktu_selesai')): ?>
                    <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($validator->getFirstError('waktu_selesai')) ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Warning -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-yellow-800">Perhatian</p>
                        <p class="text-sm text-yellow-700 mt-1">
                            Setelah reschedule, status booking akan kembali ke <strong>Pending</strong> dan memerlukan
                            verifikasi ulang.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit"
                    class="flex-1 bg-amber-500 text-white px-8 py-4 rounded-xl hover:bg-amber-600 transition-all font-semibold shadow-lg hover:shadow-xl flex items-center justify-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Reschedule
                </button>
                <a href="/admin/bookings/detail?id=<?= (int) $booking->id_booking ?>"
                    class="px-8 py-4 rounded-xl bg-gray-200 text-gray-700 hover:bg-gray-300 transition-all font-semibold flex items-center justify-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>