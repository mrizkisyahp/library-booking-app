<?php

use App\Core\App;
use App\Models\Booking;
use App\Core\Csrf;
use App\Models\User;

$currentUser = App::$app->user instanceof User ? App::$app->user : null;

?>

<!-- echo '<pre>';
print_r($bookings);
echo '</pre>'; -->

<div class="min-h-screen bg-linear-to-br from-slate-50 to-slate-100">
    <div class="max-w-7xl mx-auto px-6 py-12">
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-4xl font-bold text-slate-800 mb-2">Riwayat Booking</h2>
            <p class="text-slate-600">Monitor seluruh riwayat ruangan yang pernah digunakan</p>
        </div>

        <!-- Filter Form -->
        <form method="get" action="/my-bookings" class="relative bg-white rounded-2xl shadow-lg p-8 mb-4">
            <?= csrf_field() ?>

            <!-- hidden checkbox peer untuk toggle panel -->
            <input type="checkbox" id="filterToggle" class="peer hidden" />

            <!-- Search input -->
            <div class="relative w-full mb-4">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>

                <input type="text" name="nama_ruangan" value="<?= htmlspecialchars($filters['nama_ruangan'] ?? '') ?>"
                    placeholder="Cari nama ruangan..."
                    class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-2xl
                      focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:outline-none transition-all" />
            </div>

            <!-- tombol filter: ini adalah label untuk #filterToggle -->
            <label for="filterToggle" class="w-full cursor-pointer flex items-center justify-center gap-3
                    py-3 px-4 border-2 border-gray-200 rounded-2xl text-gray-600
                    hover:bg-gray-100 transition">
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

            <!-- PANEL FILTER (sibling dari input#filterToggle di dalam form) -->
            <div class="fixed left-0 right-0 bottom-0 h-8/10 z-999 bg-white
               translate-y-full peer-checked:translate-y-0
               transition-transform duration-400 ease-in-out
               rounded-t-3xl shadow-xl px-8 py-8 overflow-auto">

                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold">Filter</h2>

                    <label for="filterToggle" class="cursor-pointer p-2 rounded-full hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <path d="M18 6 6 18" />
                            <path d="M6 6 18 18" />
                        </svg>
                    </label>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Peminjaman</label>
                        <input type="date" name="tanggal" value="<?= htmlspecialchars($filters['tanggal'] ?? '') ?>"
                            class="w-full mt-1 border-2 border-gray-200 rounded-2xl py-3 px-4 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Waktu Mulai</label>
                        <input type="time" name="waktu_mulai"
                            value="<?= htmlspecialchars($filters['waktu_mulai'] ?? '') ?>"
                            class="w-full mt-1 border-2 border-gray-200 rounded-2xl py-3 px-4 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Kapasitas (min)</label>
                        <input type="number" name="kapasitas_min" min="0"
                            value="<?= htmlspecialchars($filters['kapasitas_min'] ?? '') ?>"
                            class="w-full mt-1 border-2 border-gray-200 rounded-2xl py-3 px-4 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:outline-none" />
                    </div>

                    <div>
                        <p class="block text-sm font-medium text-slate-700 mb-2">Jenis Ruangan</p>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($roomTypes as $roomType):
                                $safeId = 'rt_' . preg_replace('/[^a-z0-9]+/i', '_', strtolower($roomType));
                                ?>
                                <label for="<?= $safeId ?>"
                                    class="flex items-center bg-slate-100 py-2 px-4 border-2 border-gray-300 rounded-2xl cursor-pointer checked:bg-emerald-600 checked:text-white">
                                    <input id="<?= $safeId ?>" type="checkbox" name="jenis_ruangan[]"
                                        value="<?= htmlspecialchars($roomType) ?>" class="appearance-none "
                                        <?= in_array($roomType, $filters['jenis_ruangan'] ?? []) ? 'checked' : '' ?> />
                                    <span class="text-sm"><?= htmlspecialchars($roomType) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

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
</div>
