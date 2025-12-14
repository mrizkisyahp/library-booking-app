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

<div class="min-h-dvh">
    <!-- Header -->
    <div class="w-full bg-white p-6 mb-8">
        <div class="mb-4 mx-auto md:px-6 ">
            <h2 class="text-4xl font-bold text-slate-800 mb-2">Cari Ruangan</h2>
            <p class="text-slate-600">Cari ruangan yang ingin dipinjam</p>
        </div>

        <?php if (!auth()->user()->isAdmin() && isLibraryEffectivelyClosed()): ?>
            <!-- Library Closed Alert Banner -->
            <?php $closureReason = getClosureReason(date('Y-m-d')); ?>
            <div class="mb-4 mx-auto md:px-6">
                <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mr-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-blue-900">Perpustakaan Sedang Tutup</h3>
                            <p class="text-blue-800 mt-1 text-sm">
                                Anda dapat melihat ruangan tetapi tidak dapat membuat booking saat ini.
                            </p>
                            <?php if ($closureReason): ?>
                                <div class="mt-2 p-2 bg-white rounded border border-blue-200">
                                    <p class="text-sm text-blue-900"><strong>Alasan:</strong>
                                        <?= htmlspecialchars($closureReason) ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 px-6 mb-8 mx-6">

            <div class="md:col-span-3 md:grid md:grid-cols-3 gap-6">

                <?php foreach ($rooms as $room): ?>
                    <?php $thumbnail = room_thumbnail($room); ?>

                    <div class="bg-white rounded-3xl shadow-lg h-fit overflow-hidden">

                        <!-- Thumbnail -->
                        <?php if ($thumbnail): ?>
                            <img src="<?= $thumbnail ?>" class="w-full h-48 object-cover"
                                alt="<?= htmlspecialchars($room->nama_ruangan) ?>">
                        <?php else: ?>
                            <div class="w-full h-48 bg-slate-200 flex items-center justify-center">
                                <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        <?php endif; ?>

                        <!-- Konten Ruangan -->
                        <div class="p-6">
                            <p class="font-bold text-2xl mb-1"><?= htmlspecialchars($room->nama_ruangan) ?></p>
                            <p class="text-gray-500 mb-4"><?= htmlspecialchars($room->jenis_ruangan) ?></p>

                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2 text-slate-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path d="M18 21a8 8 0 0 0-16 0" />
                                        <circle cx="10" cy="8" r="5" />
                                    </svg>
                                    <span class="font-semibold">
                                        <?= (int) $room->kapasitas_min ?> - <?= (int) $room->kapasitas_max ?> orang
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 text-slate-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path
                                            d="m11.5 2.3 2.3 4.7c.3.7 1 1.1 1.6 1.2l5.2.8-3.7 3.6c-.5.5-.7 1.1-.6 1.8l.9 5.1-4.6-2.4c-.6-.3-1.3-.3-1.9 0L6.4 21l.9-5.1c.1-.7-.1-1.3-.6-1.8L2.9 9l5.2-.8c.7-.1 1.3-.5 1.6-1.2z" />
                                    </svg>
                                    <span><?= $room->avg_rating ? number_format($room->avg_rating, 1) : '-' ?></span>
                                </div>
                            </div>

                            <a href="/rooms/show?id_ruangan=<?= (int) $room->id_ruangan ?>"
                                class="block text-center bg-primary w-full text-white py-3 rounded-2xl hover:bg-emerald-700 font-semibold transition">
                                Lihat Detail Ruangan
                            </a>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <form method="get" action="/rooms" class="hidden md:block  h-fit">
                <div class="border border-gray-200 bg-white rounded-3xl shadow-xl px-8 py-8">
                    <h2 class="text-2xl font-bold mb-6">Filter</h2>

                    <div class="space-y-6">

                        <!-- Kapasitas -->
                        <div>
                            <label class="text-sm text-slate-700 mb-2 block">Kapasitas (min)</label>
                            <input type="number" name="kapasitas_min"
                                value="<?= htmlspecialchars($filters['kapasitas_min'] ?? '') ?>"
                                class="w-full border-2 border-gray-200 rounded-2xl px-4 py-2 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition">
                        </div>

                        <!-- Jenis Ruangan -->
                        <div>
                            <p class="text-sm font-medium text-slate-700 mb-2">Jenis Ruangan</p>

                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($roomTypes as $roomType):
                                    $safeId = 'rt_' . preg_replace('/[^a-z0-9]+/i', '_', strtolower($roomType)); ?>
                                    <div class="relative">
                                        <input id="<?= $safeId ?>" type="checkbox" name="jenis_ruangan[]"
                                            value="<?= htmlspecialchars($roomType) ?>"
                                            class="peer absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                            <?= in_array($roomType, $filters['jenis_ruangan'] ?? []) ? 'checked' : '' ?>>

                                        <label for="<?= $safeId ?>"
                                            class="peer-checked:bg-primary peer-checked:text-white peer-checked:border-primary flex items-center py-2 px-4 border-2 border-gray-300 rounded-2xl cursor-pointer">
                                            <span class="text-sm"><?= htmlspecialchars($roomType) ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Action -->
                        <div class="flex gap-3">
                            <button type="submit"
                                class="flex-1 px-4 py-3 bg-emerald-600 text-white rounded-2xl font-semibold hover:bg-emerald-700 transition">
                                Terapkan
                            </button>

                            <a href="/rooms"
                                class="flex-1 px-4 py-3 border-2 border-slate-300 rounded-2xl text-slate-700 text-center hover:bg-slate-100 transition">
                                Reset
                            </a>
                        </div>

                    </div>

                </div>

            </form>
        </div>
    <?php endif; ?>
</div>