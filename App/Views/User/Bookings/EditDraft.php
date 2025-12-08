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
<!-- Header -->
<div class="p-4 bg-white shadow-md w-full">
    <div class="flex items-center gap-4 py-4">
        <div class="flex items-center gap-4">
            <a href="/bookings/draft?id=<?= (int) $booking->id_booking ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-chevron-left-icon lucide-chevron-left size-9">
                    <path d="m15 18-6-6 6-6" />
                </svg>
            </a>
            <span class="text-black font-bold text-4xl">
                Edit Draft Booking
            </span>
        </div>
    </div>
</div>
<div class="p-6 bg-gray-200 min-h-screen">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <form action="/bookings/update-draft" method="post" class="space-y-6">
                <?= csrf_field() ?>
                <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
                <!-- Room Selection -->
                <div>
                    <label for="ruangan_id" class="block text-sm font-semibold text-slate-700 mb-2">Ruangan</label>
                    <select name="ruangan_id" id="ruangan_id" required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                        <?php foreach ($rooms as $room): ?>
                            <option value="<?= (int) $room->id_ruangan ?>" <?= $booking->ruangan_id == $room->id_ruangan ? 'selected' : '' ?>>
                                <?= htmlspecialchars($room->nama_ruangan) ?>
                                (Min: <?= (int) $room->kapasitas_min ?>, Max: <?= (int) $room->kapasitas_max ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Date -->
                <div>
                    <label for="tanggal_penggunaan_ruang"
                        class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Penggunaan</label>
                    <input type="date" name="tanggal_penggunaan_ruang" id="tanggal_penggunaan_ruang" required
                        value="<?= htmlspecialchars($booking->tanggal_penggunaan_ruang) ?>"
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                </div>
                <!-- Time Inputs -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="waktu_mulai" class="block text-sm font-semibold text-slate-700 mb-2">Waktu
                            Mulai</label>
                        <input type="time" name="waktu_mulai" id="waktu_mulai" required
                            value="<?= htmlspecialchars(substr($booking->waktu_mulai, 0, 5)) ?>"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                    </div>
                    <div>
                        <label for="waktu_selesai" class="block text-sm font-semibold text-slate-700 mb-2">Waktu
                            Selesai</label>
                        <input type="time" name="waktu_selesai" id="waktu_selesai" required
                            value="<?= htmlspecialchars(substr($booking->waktu_selesai, 0, 5)) ?>"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                    </div>
                </div>
                <!-- Tujuan -->
                <div>
                    <label for="tujuan" class="block text-sm font-semibold text-slate-700 mb-2">Tujuan
                        Penggunaan</label>
                    <select name="tujuan" id="tujuan" required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all">
                        <?php foreach ($tujuanMahasiswa as $tujuan): ?>
                            <option value="<?= htmlspecialchars($tujuan) ?>" <?= $booking->tujuan === $tujuan ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tujuan) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Buttons -->
                <div class="flex gap-4 pt-4">
                    <a href="/bookings/draft?id=<?= (int) $booking->id_booking ?>"
                        class="flex-1 bg-gray-200 text-gray-700 px-8 py-4 rounded-xl hover:bg-gray-300 transition-all font-semibold text-center">
                        Batal
                    </a>
                    <button type="submit"
                        class="flex-1 bg-primary text-white px-8 py-4 rounded-xl hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>