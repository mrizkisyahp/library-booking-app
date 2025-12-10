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
            <h2 class="text-4xl font-bold text-slate-800 mb-2">Cari Ruangan</h2>
            <p class="text-slate-600">Cari ruangan yang ingin dipinjam</p>
        </div>
        <!-- Form tempat input#filterToggle, search, tombol filter -->
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
                                        value="<?= htmlspecialchars($roomType) ?>" class="peer absolute inset-0 opacity-0 w-full h-full cursor-pointer"
                                        <?= in_array($roomType, $filters['jenis_ruangan'] ?? []) ? 'checked' : '' ?> />

                                    <label for="<?= $safeId ?>"
                                        class="peer-checked:bg-primary peer-checked:border-emerald-600 peer-checked:text-white flex items-center bg-slate-100 py-2 px-4 border-2 border-gray-300 rounded-2xl cursor-pointer ">
                                        <span class="text-sm"><?= htmlspecialchars($roomType) ?></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <p class="block text-sm font-medium text-slate-700 mb-2">Jenis Ruangan</p>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($roomTypes as $roomType):
                                $safeId = 'rt_' . preg_replace('/[^a-z0-9]+/i', '_', strtolower($roomType));
                                ?>
                                <label for="<?= $safeId ?>"
                                    class="flex items-center bg-slate-100 py-2 px-4 border-2 border-gray-300 rounded-2xl cursor-pointer ">
                                    <input id="<?= $safeId ?>" type="checkbox" name="jenis_ruangan[]"
                                        value="<?= htmlspecialchars($roomType) ?>" class="appearance-none "
                                        <?= in_array($roomType, $filters['jenis_ruangan'] ?? []) ? 'checked' : '' ?> />
                                    <span class="text-sm"><?= htmlspecialchars($roomType) ?></span>
                                </label>
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

    <!-- Room List -->
    <?php if (empty($rooms)): ?>
        <div class="bg-white rounded-2xl shadow-lg p-16 text-center">
            <svg class="w-24 h-24 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h3 class="text-xl font-semibold text-slate-700 mb-2">Tidak Ada Ruangan Ditemukan</h3>
            <p class="text-slate-500">Coba ubah filter pencarian Anda</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 px-6 mb-8">
            <!-- loop ruangan -->
            <div class="md:col-span-3 md:grid md:grid-cols-3 gap-4 mx-auto space-y-6 p-6">
                <?php foreach ($rooms as $room): ?>
                    <?php $thumbnail = room_thumbnail($room); ?>
                    <div class="bg-white rounded-3xl shadow-lg h-fit ">
                        <div>
                            <div>
                                <!-- Blocked Overlay for rejected/pending users -->
                                <?php if (auth()->user()->status === 'pending kubaca' || auth()->user()->status === 'rejected'): ?>
                                    <div>
                                        <div
                                            class="bg-slate-900/90 backdrop-blur-sm text-white px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 shadow-lg">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            View Only
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="">
                                    <div>
                                        <p>
                                            <?php if ($thumbnail): ?>
                                                <img src="<?= $thumbnail ?>" alt="<?= htmlspecialchars($room->nama_ruangan) ?>"
                                                    class="w-full h-full object-cover rounded-t-3xl">
                                            <?php else: ?>
                                            <div
                                                class="w-48 bg-linear-to-br from-slate-200 to-slate-300 flex items-center justify-center">
                                                <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                        </p>
                                        <div class="p-6 mb-6">
                                            <p class="font-bold text-4xl">
                                                <?= htmlspecialchars($room->nama_ruangan) ?>
                                            </p>
                                            <p class="text-gray-400 mb-4">
                                                <?= htmlspecialchars($room->jenis_ruangan) ?>
                                            </p>
                                            <div class="flex items-center gap-4">
                                                <p class="flex gap-4 items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-users-round-icon lucide-users-round size-4">
                                                        <path d="M18 21a8 8 0 0 0-16 0" />
                                                        <circle cx="10" cy="8" r="5" />
                                                        <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3" />
                                                    </svg>
                                                    <span>
                                                        <span class="font-semibold"><?= (int) $room->kapasitas_min ?> -
                                                            <?= (int) $room->kapasitas_max ?>
                                                        </span>
                                                        orang
                                                    </span>
                                                </p>
                                                <p class="flex items-center gap-4">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-star-icon lucide-star size-4">
                                                        <path
                                                            d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z" />
                                                    </svg>
                                                    <span>
                                                        Rating
                                                        <?= $room->avg_rating ? number_format($room->avg_rating, 1) : '-' ?>
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="p-4 mt-4">
                                            <a href="/rooms/show?id_ruangan=<?= (int) $room->id_ruangan ?>"
                                                class="inline-flex items-center text-white rounded-2xl p-4 w-full justify-center capitalize bg-primary hover:bg-emerald-700 font-semibold focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 group">
                                                Lihat Detail ruangan
                                                <svg class="w-5 h-5 ml-1 transform group-hover:translate-x-1 transition-transform"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <form method="get" action="/rooms" class="relative mb-4 max-w-7xl mx-auto md:px-6">

                <div class="border border-gray-200 bg-white
                rounded-3xl shadow-xl px-8 py-8 hidden md:block h-fit sticky top-0">

                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold">Filter</h2>
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
                                        value="<?= htmlspecialchars($roomType) ?>" class="peer absolute inset-0 opacity-0 w-full h-full cursor-pointer"
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
    <?php endif; ?>
</div>
