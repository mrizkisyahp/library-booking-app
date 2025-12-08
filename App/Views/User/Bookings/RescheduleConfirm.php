<div class="max-w-2xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="/bookings/reschedule?id=<?= (int) $booking->id_booking ?>"
            class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold group">
            <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Ubah Jadwal
        </a>
    </div>

    <!-- Page Header -->
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
        <h2 class="text-3xl font-bold text-slate-800 flex items-center mb-2">
            <svg class="w-8 h-8 mr-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Konfirmasi Reschedule
        </h2>
        <p class="text-slate-600">Periksa perubahan jadwal sebelum mengonfirmasi</p>
    </div>

    <!-- Comparison Card -->
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
        <h3 class="text-xl font-bold text-slate-800 mb-6 text-center">Perbandingan Jadwal</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Old Schedule -->
            <div class="bg-red-50 rounded-xl p-6 border-2 border-red-200">
                <div class="flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <h4 class="font-bold text-red-800">Jadwal Lama</h4>
                </div>
                <div class="space-y-3 text-center">
                    <div>
                        <p class="text-sm text-red-600">Tanggal</p>
                        <p class="text-lg font-bold text-red-800">
                            <?= date('d M Y', strtotime($booking->tanggal_penggunaan_ruang)) ?></p>
                        <p class="text-sm text-red-700"><?= date('l', strtotime($booking->tanggal_penggunaan_ruang)) ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-red-600">Waktu</p>
                        <p class="text-lg font-bold text-red-800">
                            <?= htmlspecialchars(substr($booking->waktu_mulai, 0, 5)) ?> -
                            <?= htmlspecialchars(substr($booking->waktu_selesai, 0, 5)) ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- New Schedule -->
            <div class="bg-green-50 rounded-xl p-6 border-2 border-green-200">
                <div class="flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <h4 class="font-bold text-green-800">Jadwal Baru</h4>
                </div>
                <div class="space-y-3 text-center">
                    <div>
                        <p class="text-sm text-green-600">Tanggal</p>
                        <p class="text-lg font-bold text-green-800"><?= date('d M Y', strtotime($newDate)) ?></p>
                        <p class="text-sm text-green-700"><?= date('l', strtotime($newDate)) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-green-600">Waktu</p>
                        <p class="text-lg font-bold text-green-800">
                            <?= htmlspecialchars(substr($newStart, 0, 5)) ?> -
                            <?= htmlspecialchars(substr($newEnd, 0, 5)) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Arrow indicator for mobile -->
        <div class="flex justify-center my-4 md:hidden">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>
    </div>

    <!-- Room Info -->
    <div class="bg-slate-50 rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-emerald-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <div>
                <p class="text-sm text-slate-600">Ruangan</p>
                <p class="font-bold text-slate-800"><?= htmlspecialchars($booking->nama_ruangan) ?></p>
            </div>
        </div>
    </div>

    <!-- Warning -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="ml-3">
                <p class="text-sm font-semibold text-yellow-800">Perhatian</p>
                <p class="text-sm text-yellow-700 mt-1">
                    Setelah mengonfirmasi, status booking akan kembali ke <strong>Pending</strong> dan memerlukan
                    persetujuan admin kembali. Kode check-in sebelumnya tidak akan berlaku lagi.
                </p>
            </div>
        </div>
    </div>

    <!-- Confirm Form -->
    <form action="/bookings/reschedule" method="post" class="space-y-4">
        <?= csrf_field() ?>
        <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
        <input type="hidden" name="tanggal_penggunaan_ruang" value="<?= htmlspecialchars($newDate) ?>">
        <input type="hidden" name="waktu_mulai" value="<?= htmlspecialchars($newStart) ?>">
        <input type="hidden" name="waktu_selesai" value="<?= htmlspecialchars($newEnd) ?>">

        <button type="submit"
            class="w-full bg-emerald-600 text-white px-8 py-4 rounded-xl hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl flex items-center justify-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Konfirmasi Reschedule
        </button>

        <a href="/bookings/detail?id=<?= (int) $booking->id_booking ?>"
            class="w-full bg-gray-200 text-gray-700 px-8 py-4 rounded-xl hover:bg-gray-300 transition-all font-semibold flex items-center justify-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            Batal
        </a>
    </form>
</div>