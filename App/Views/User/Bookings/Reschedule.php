<!-- Back Button -->
<div class="p-4 bg-white shadow-md w-full">
    <div class="flex items-center gap-4 py-4">
        <div class="flex items-center gap-4 ">
            <a href="/bookings/detail?id=<?= (int) $booking->id_booking ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-chevron-left-icon lucide-chevron-left size-9">
                    <path d="m15 18-6-6 6-6" />
                </svg>
            </a>
            <span class="text-black font-bold text-4xl">
                Kembali ke Detail Booking
            </span>
        </div>
    </div>
</div>

<div class="max-w-2xl mx-auto mt-6">

    <!-- Page Header -->
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
        <h2 class="text-3xl font-bold text-slate-800 flex items-center mb-2">
            <svg class="w-8 h-8 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Reschedule Booking
        </h2>
        <p class="text-slate-600">Pilih tanggal dan waktu baru untuk booking Anda</p>
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
                <p class="text-slate-800 font-bold"><?= htmlspecialchars($booking->nama_ruangan) ?></p>
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

        <form action="/bookings/reschedule/confirm" method="post" class="space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">

            <!-- Date -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Penggunaan</label>
                <input type="date" name="tanggal_penggunaan_ruang" required min="<?= date('Y-m-d') ?>"
                    max="<?= date('Y-m-d', strtotime('+14 days')) ?>"
                    value="<?= htmlspecialchars($booking->tanggal_penggunaan_ruang) ?>"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                <p class="text-xs text-slate-500 mt-1">Maksimal 7 hari kerja ke depan, tidak termasuk weekend</p>
            </div>

            <!-- Start Time -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Waktu Mulai</label>
                <input type="time" name="waktu_mulai" required min="07:00" max="16:00"
                    value="<?= htmlspecialchars(substr($booking->waktu_mulai, 0, 5)) ?>"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                <p class="text-xs text-slate-500 mt-1">Jam operasional: 07:00 - 17:00</p>
            </div>

            <!-- End Time -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Waktu Selesai</label>
                <input type="time" name="waktu_selesai" required min="08:00" max="17:00"
                    value="<?= htmlspecialchars(substr($booking->waktu_selesai, 0, 5)) ?>"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
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
                            persetujuan admin kembali.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full bg-amber-500 text-white px-8 py-4 rounded-xl hover:bg-amber-600 transition-all font-semibold shadow-lg hover:shadow-xl flex items-center justify-center cursor-pointer">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Lanjutkan ke Konfirmasi
            </button>
        </form>
    </div>
</div>