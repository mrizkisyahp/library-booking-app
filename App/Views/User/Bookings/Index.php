<?php
$roomTypes = [
    'Audio Visual',
    'Telekonferensi',
    'Kreasi dan Rekreasi',
    'Baca Kelompok',
    'Koleksi Bahasa Prancis',
    'Bimbingan & Konseling',
];

?>

<div class="min-h-dvh bg-slate-100">
    <!-- Header -->
    <div class="w-full bg-white p-6 mb-8">
        <div class="mb-4 mx-auto md:px-6 ">
            <h2 class="text-4xl font-bold text-slate-800 mb-2">Riwayat Booking</h2>
            <p class="text-slate-600">Monitor seluruh riwayat ruangan yang pernah digunakan</p>
        </div>

        <!-- Filter Form -->
        <form method="get" action="/rooms" class="relative mb-4 mx-auto md:px-6">

            <!-- Toggle Modal -->
            <input type="checkbox" id="filterToggle" class="peer hidden" />

            <!-- Search -->
            <div class="relative w-full mb-4">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-900 pointer-events-none"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>

                <input type="text" name="nama_ruangan" value="<?= htmlspecialchars($filters['nama_ruangan'] ?? '') ?>"
                    placeholder="Cari nama ruangan..."
                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-2xl bg-slate-100
                focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:outline-none transition-all placeholder-gray-500" />
            </div>

            <!-- Button panel -->
            <label for="filterToggle" class="w-full cursor-pointer flex items-center justify-center gap-3 bg-slate-100 py-3 px-4 border-2 border-gray-200 rounded-2xl text-gray-600 md:hidden
                hover:bg-gray-200 transition-all active:scale-[0.97] duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M10 5H3" />
                    <path d="M12 19H3" />
                    <path d="M14 3v4" />
                    <path d="M16 17v4" />
                    <path d="M21 12h-9" />
                    <path d="M21 19h-5" />
                    <path d="M21 5h-7" />
                    <path d="M8 10v4" />
                    <path d="M8 12H3" />
                </svg>
                <span>Filter</span>
            </label>

            <!-- Modal Filter -->
            <div class="fixed left-0 right-0 bottom-0 h-80vh z-999 border border-gray-200 bg-white translate-y-full peer-checked:translate-y-0 transition-transform duration-500 ease-in-out
                rounded-t-3xl shadow-xl px-8 py-8 overflow-y-auto">

                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold">Filter</h2>
                    <label for="filterToggle"
                        class="cursor-pointer p-2 rounded-full hover:bg-gray-100 active:scale-95 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M18 6 6 18" />
                            <path d="M6 6 18 18" />
                        </svg>
                    </label>
                </div>

                <div class="space-y-6">

                    <!-- Tanggal -->
                    <div class="group relative">
                        <label class="block text-sm text-slate-700 mb-2">Tanggal</label>

                        <div
                            class="flex items-center gap-3 border-2 border-gray-200 rounded-2xl px-4 py-2 bg-white shadow
                            transition-all duration-300 group-hover:border-gray-300 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-200 focus-within:shadow-md">

                            <label for="tanggal_peminjaman"
                                class="cursor-pointer text-gray-400 group-focus-within:text-emerald-600 group-hover:text-gray-500">

                                <!-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="size-6 text-gray-400 group-focus-within:text-emerald-600 transition">
                                    <path d="M8 2v4" />
                                    <path d="M16 2v4" />
                                    <rect width="18" height="18" x="3" y="4" rx="2" />
                                    <path d="M3 10h18" />
                                </svg> -->
                            </label>

                            <input id="tanggal_peminjaman" type="date" name="tanggal"
                                value="<?= htmlspecialchars($filters['tanggal'] ?? '') ?>"
                                class="flex-1 bg-transparent appearance-none text-slate-700 cursor-pointer focus:outline-none transition-all duration-200 placeholder-gray-400">
                        </div>
                    </div>

                    <!-- Waktu -->
                    <div class="group relative">
                        <label class="block text-sm text-slate-700 mb-2">Waktu Mulai</label>

                        <div
                            class="flex items-center gap-3 border-2 border-gray-200 rounded-2xl px-4 py-2 bg-white shadow
                            transition-all duration-300 group-hover:border-gray-300 focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-200 focus-within:shadow-md">

                            <label for="waktu_mulai">
                                <!-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="size-6 text-gray-400 group-focus-within:text-emerald-600 transition">
                                    <path d="M12 6v6l4 2" />
                                    <circle cx="12" cy="12" r="10" />
                                </svg> -->
                            </label>

                            <input type="time" name="waktu_mulai" id="waktu_mulai"
                                value="<?= htmlspecialchars($filters['waktu_mulai'] ?? '') ?>"
                                class="flex-1 bg-transparent appearance-none text-slate-700 cursor-pointer focus:outline-none transition-all duration-200 placeholder-gray-400">
                        </div>
                    </div>

                    <!-- Kapasitas -->
                    <div class="group relative">
                        <label class="block text-sm text-slate-700 mb-2">Kapasitas (min)</label>

                        <div class="flex items-center gap-3 border-2 border-gray-200 rounded-2xl px-4 py-2 bg-white shadow
                            transition-all duration-300 group-hover:border-gray-300 focus-within:border-emerald-500
                            focus-within:ring-2 focus-within:ring-emerald-200">

                            <span class="text-gray-400 group-hover:text-gray-500 group-focus-within:text-emerald-600">
                                #
                            </span>

                            <input type="number" name="kapasitas_min" min="0"
                                value="<?= htmlspecialchars($filters['kapasitas_min'] ?? '') ?>"
                                class="flex-1 bg-transparent focus:outline-none text-slate-700" />
                        </div>
                    </div>


                    <!-- Jenis Ruangan -->
                    <div>
                        <p class="block text-sm font-medium text-slate-700 mb-2">Jenis Ruangan</p>

                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($roomTypes as $roomType):
                                $safeId = 'rt_' . preg_replace('/[^a-z0-9]+/i', '_', strtolower($roomType));
                                ?>
                                <div class="relative">
                                    <input id="<?= $safeId ?>" type="checkbox" name="jenis_ruangan[]"
                                        value="<?= htmlspecialchars($roomType) ?>"
                                        class="peer absolute inset-0 opacity-0 w-full h-full cursor-pointer"
                                        <?= in_array($roomType, $filters['jenis_ruangan'] ?? []) ? 'checked' : '' ?> />

                                    <label for="<?= $safeId ?>"
                                        class="peer-checked:bg-primary peer-checked:border-emerald-600 peer-checked:text-white flex items-center bg-slate-100 py-2 px-4 border-2 border-gray-300 rounded-2xl cursor-pointer ">
                                        <span class="text-sm"><?= htmlspecialchars($roomType) ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 px-4 py-3 bg-emerald-600 text-white rounded-2xl font-semibold
                        hover:bg-emerald-700 transition active:scale-95">
                            Terapkan
                        </button>

                        <a href="/rooms" class="flex-1 px-4 py-3 border-2 border-slate-300 rounded-2xl text-slate-700 text-center
                        hover:bg-slate-100 transition active:scale-95">
                            Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 px-6 mb-8 mx-6">

        <!-- loop data booking -->
        <div class="md:col-span-3 space-y-6">
            <?php foreach ($bookings as $booking): ?>
                <div class="rounded-3xl border-2 border-gray-300 bg-gray-100 shadow-lg p-6">
                    <!-- NAMA RUANGAN -->
                    <p class="font-bold text-2xl mb-1">
                        <?= htmlspecialchars($booking->nama_ruangan) ?>
                    </p>
                    <p class="text-slate-600 mb-3">
                        <?= htmlspecialchars($booking->jenis_ruangan) ?>
                    </p>
                    <!-- STATUS -->
                    <div class="mb-4">
                        <?php
                        $statusColors = [
                            'draft' => 'text-gray-800',
                            'pending' => 'text-yellow-700',
                            'verified' => 'text-blue-700',
                            'active' => 'text-emerald-700',
                            'completed' => 'text-green-700',
                            'cancelled' => 'text-red-700',
                            'expired' => 'text-slate-700',
                            'no_show' => 'text-orange-700',
                        ];

                        $statusKey = strtolower($booking->status);
                        $statusLabel = ucwords(str_replace('_', ' ', $statusKey));
                        ?>

                        <span class="font-semibold">Status:</span>
                        <span class="<?= $statusColors[$statusKey] ?? '' ?>">
                            <?= $statusLabel ?>
                        </span>

                        <!-- Tambahan status detail -->
                        <?php if ($booking->status === 'draft' && $booking->current_members < $booking->required_members): ?>
                            (Menunggu Anggota)
                        <?php elseif ($booking->status === 'draft'): ?>
                            (Siap Dikirim)
                        <?php elseif ($booking->status === 'pending'): ?>
                            (Menunggu Konfirmasi)
                        <?php elseif ($booking->status === 'verified'): ?>
                            (Terkonfirmasi)
                        <?php elseif ($booking->status === 'active'): ?>
                            (Sedang berlangsung)
                        <?php elseif ($booking->status === 'completed'): ?>
                            (Selesai)
                        <?php endif; ?>
                    </div>

                    <!-- TANGGAL -->
                    <p class="mb-3 flex items-center gap-2 text-slate-700">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M8 2v4" />
                            <path d="M16 2v4" />
                            <rect width="18" height="18" x="3" y="4" rx="2" />
                            <path d="M3 10h18" />
                        </svg>
                        <?= htmlspecialchars(formatTanggal($booking->tanggal_penggunaan_ruang)) ?>
                    </p>

                    <!-- WAKTU -->
                    <p class="mb-3 flex items-center gap-2 text-slate-700">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 6v6h4" />
                            <circle cx="12" cy="12" r="10" />
                        </svg>
                        <?= htmlspecialchars(formatWaktu($booking->waktu_mulai)) ?>
                    </p>

                    <!-- PESERTA -->
                    <p class="mb-4 flex items-center gap-2 text-slate-700">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M18 21a8 8 0 0 0-16 0" />
                            <circle cx="10" cy="8" r="5" />
                            <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3" />
                        </svg>

                        <?= (int) $booking->current_members ?> /
                        <?= $booking->maximum_members > 0 ? (int) $booking->maximum_members : '∞' ?>
                        peserta

                        <?php if ($booking->required_members > 0): ?>
                            · Min <?= (int) $booking->required_members ?>
                        <?php endif; ?>
                    </p>

                    <!-- ACTION BUTTONS -->
                    <div class="space-y-3">

                        <?php if ($booking->status === 'draft'): ?>
                            <a href="/bookings/draft?id=<?= (int) $booking->id_booking ?>"
                                class="block bg-emerald-600 hover:bg-emerald-700 text-white text-center px-4 py-2 rounded-xl shadow transition">
                                Lihat Draft
                            </a>
                        <?php else: ?>
                            <a href="/bookings/detail?id=<?= (int) $booking->id_booking ?>"
                                class="block bg-emerald-600 hover:bg-emerald-700 text-white text-center px-4 py-2 rounded-xl shadow transition">
                                Lihat Detail
                            </a>
                        <?php endif; ?>

                        <?php if ($booking->status === 'completed' && empty($booking->id_feedback)): ?>
                            <a href="/feedback/create?booking=<?= (int) $booking->id_booking ?>"
                                class="block text-emerald-600 underline hover:text-emerald-700 text-center px-4 py-2 rounded-xl transition">
                                Isi Feedback
                            </a>
                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <!-- filter desktop -->
        <form method="get" action="/my-bookings" class="hidden md:block sticky top-0 h-fit">

            <div class="border border-gray-200 bg-white rounded-3xl shadow-xl px-8 py-8">

                <h2 class="text-2xl font-bold mb-6">Filter</h2>

                <div class="space-y-6">

                    <!-- TANGGAL -->
                    <div class="group">
                        <label class="text-sm text-slate-700 mb-2 block">Tanggal</label>

                        <input type="date" name="tanggal" value="<?= htmlspecialchars($filters['tanggal'] ?? '') ?>"
                            class="w-full border-2 border-gray-200 rounded-2xl px-4 py-2 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                    </div>

                    <!-- WAKTU -->
                    <div class="group">
                        <label class="text-sm text-slate-700 mb-2 block">Waktu Mulai</label>

                        <input type="time" name="waktu_mulai"
                            value="<?= htmlspecialchars($filters['waktu_mulai'] ?? '') ?>"
                            class="w-full border-2 border-gray-200 rounded-2xl px-4 py-2 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                    </div>

                    <!-- KAPASITAS -->
                    <div>
                        <label class="text-sm text-slate-700 mb-2 block">Kapasitas (min)</label>

                        <input type="number" name="kapasitas_min" min="0"
                            value="<?= htmlspecialchars($filters['kapasitas_min'] ?? '') ?>"
                            class="w-full border-2 border-gray-200 rounded-2xl px-4 py-2 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                    </div>

                    <!-- jenis ruangan -->
                    <div>
                        <p class="text-sm font-medium text-slate-700 mb-2">Jenis Ruangan</p>

                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($roomTypes as $roomType):
                                $safeId = 'rt_' . preg_replace('/[^a-z0-9]+/i', '_', strtolower($roomType));
                                ?>
                                <div class="relative">
                                    <input id="<?= $safeId ?>" type="checkbox" name="jenis_ruangan[]"
                                        value="<?= htmlspecialchars($roomType) ?>"
                                        class="peer absolute inset-0 opacity-0 w-full h-full cursor-pointer"
                                        <?= in_array($roomType, $filters['jenis_ruangan'] ?? []) ? 'checked' : '' ?> />

                                    <label for="<?= $safeId ?>"
                                        class="peer-checked:bg-primary peer-checked:border-emerald-600 peer-checked:text-white flex items-center bg-slate-100 py-2 px-4 border-2 border-gray-300 rounded-2xl cursor-pointer ">
                                        <span class="text-sm"><?= htmlspecialchars($roomType) ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- ACTION -->
                    <div class="flex gap-3">
                        <button type="submit"
                            class="flex-1 px-4 py-3 bg-emerald-600 text-white rounded-2xl font-semibold hover:bg-emerald-700 transition active:scale-95">
                            Terapkan
                        </button>

                        <a href="/my-bookings"
                            class="flex-1 px-4 py-3 border-2 border-slate-300 rounded-2xl text-center hover:bg-slate-100 text-slate-700 transition active:scale-95">
                            Reset
                        </a>
                    </div>

                </div>

            </div>

        </form>

    </div>

</div>

<!-- Pagination -->
<?php
$paginationQuery = array_filter($filters, fn($value) => $value !== '' && $value !== []);
?>
<div class="bg-white rounded-2xl shadow-lg p-6 mt-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-slate-600">
            Menampilkan <span
                class="font-semibold text-slate-800"><?= (($pagination->currentPage - 1) * $pagination->perPage) + 1 ?></span>
            sampai <span
                class="font-semibold text-slate-800"><?= min($pagination->currentPage * $pagination->perPage, $pagination->total) ?></span>
            dari <span class="font-semibold text-slate-800"><?= $pagination->total ?></span> booking
        </p>
        <div class="flex gap-2 items-center">
            <!-- First Page -->
            <?php if ($pagination->currentPage > 1): ?>
                <?php $paginationQuery['page'] = 1; ?>
                <a href="/my-bookings?<?= http_build_query($paginationQuery) ?>"
                    class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    Awal
                </a>
            <?php endif; ?>
            <!-- Previous -->
            <?php if ($pagination->currentPage > 1): ?>
                <?php $paginationQuery['page'] = $pagination->currentPage - 1; ?>
                <a href="/my-bookings?<?= http_build_query($paginationQuery) ?>"
                    class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    ← Sebelumnya
                </a>
            <?php endif; ?>
            <!-- Page Numbers -->
            <div class="flex gap-1">
                <?php for ($i = 1; $i <= $pagination->lastPage; $i++): ?>
                    <?php $paginationQuery['page'] = $i; ?>
                    <a href="/my-bookings?<?= http_build_query($paginationQuery) ?>" class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-semibold transition-all
                <?= $i === $pagination->currentPage
                    ? 'bg-emerald-600 text-white shadow-md'
                    : 'bg-slate-100 text-slate-700 hover:bg-slate-200' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <!-- Next -->
            <?php if ($pagination->currentPage < $pagination->lastPage): ?>
                <?php $paginationQuery['page'] = $pagination->currentPage + 1; ?>
                <a href="/my-bookings?<?= http_build_query($paginationQuery) ?>"
                    class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    Selanjutnya →
                </a>
            <?php endif; ?>
            <!-- Last Page -->
            <?php if ($pagination->currentPage < $pagination->lastPage): ?>
                <?php $paginationQuery['page'] = $pagination->lastPage; ?>
                <a href="/my-bookings?<?= http_build_query($paginationQuery) ?>"
                    class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                    Akhir
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
