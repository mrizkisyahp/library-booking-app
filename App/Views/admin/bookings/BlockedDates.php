<?php
use App\Core\App;
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Blocked Dates</h1>
            <p class="text-slate-600">Kelola tanggal yang diblokir untuk booking</p>
        </div>
        <a href="/admin/bookings"
            class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Bookings
        </a>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Blokir Tanggal
                </h2>

                <form action="/admin/blocked-dates" method="post" class="space-y-4">
                    <?= csrf_field() ?>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="tanggal_begin" required min="<?= date('Y-m-d') ?>"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Selesai</label>
                        <input type="date" name="tanggal_end" required min="<?= date('Y-m-d') ?>"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Ruangan (Opsional)</label>
                        <select name="ruangan_id"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all">
                            <option value="">Semua Ruangan</option>
                            <?php
                            $rooms = \App\Models\Room::Query()->orderBy('nama_ruangan')->get();
                            foreach ($rooms as $room):
                                ?>
                                <option value="<?= (int) $room->id_ruangan ?>"><?= htmlspecialchars($room->nama_ruangan) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Kosongkan untuk memblokir semua ruangan</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Alasan</label>
                        <textarea name="alasan" rows="3" placeholder="Contoh: Libur Nasional, Maintenance, dll."
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all resize-none"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-red-500 text-white px-6 py-3 rounded-xl hover:bg-red-600 transition-all font-semibold flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Blokir Tanggal
                    </button>
                </form>
            </div>
        </div>

        <!-- List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Daftar Tanggal Diblokir
                </h2>

                <?php if (empty($blockedDates)): ?>
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-slate-600">Tidak ada tanggal yang diblokir</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b-2 border-slate-200">
                                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Periode</th>
                                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Ruangan</th>
                                    <th class="text-left py-3 px-4 font-semibold text-slate-700">Alasan</th>
                                    <th class="text-center py-3 px-4 font-semibold text-slate-700">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($blockedDates as $blocked): ?>
                                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                                        <td class="py-4 px-4">
                                            <div class="font-semibold text-slate-800">
                                                <?= date('d M Y', strtotime($blocked['tanggal_begin'])) ?>
                                            </div>
                                            <?php if ($blocked['tanggal_begin'] !== $blocked['tanggal_end']): ?>
                                                <div class="text-sm text-slate-500">
                                                    s/d <?= date('d M Y', strtotime($blocked['tanggal_end'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-4">
                                            <?php if (empty($blocked['ruangan_id'])): ?>
                                                <span
                                                    class="inline-flex px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                                    Semua Ruangan
                                                </span>
                                            <?php else: ?>
                                                <?php
                                                $room = \App\Models\Room::Query()->where('id_ruangan', $blocked['ruangan_id'])->first();
                                                ?>
                                                <span
                                                    class="text-slate-800"><?= htmlspecialchars($room->nama_ruangan ?? 'Unknown') ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span
                                                class="text-slate-600"><?= htmlspecialchars($blocked['alasan'] ?? '-') ?></span>
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <form action="/admin/blocked-dates/delete" method="post" class="inline"
                                                onsubmit="return confirm('Yakin ingin menghapus blokir ini?');">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="blocked_date_id"
                                                    value="<?= (int) $blocked['id_blocked_date'] ?>">
                                                <button type="submit"
                                                    class="inline-flex items-center px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>