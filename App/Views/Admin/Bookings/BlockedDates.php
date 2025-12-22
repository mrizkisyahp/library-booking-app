<?php
use App\Core\App;
$validator = $validator ?? null;
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

    <?php if ($message = App::$app->session->getFlash('warning')): ?>
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded-lg">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php
    // Check library status - Either ruangan_id=NULL OR all rooms blocked
    $today = date('Y-m-d');
    $libraryClosedToday = libraryIsClosedToday(); // Check for ruangan_id = NULL
    $closureReason = $libraryClosedToday ? getClosureReason($today) : null;

    // Also check if ALL individual rooms are blocked for today
    if (!$libraryClosedToday && !empty($rooms)) {
        $totalRooms = count($rooms);
        $blockedRoomIds = [];

        foreach ($blockedDates as $block) {
            // Check if this block covers today and has a specific room
            if (
                $block['ruangan_id'] !== null
                && $block['tanggal_begin'] <= $today
                && $block['tanggal_end'] >= $today
            ) {
                $blockedRoomIds[] = $block['ruangan_id'];
            }
        }

        $blockedRoomIds = array_unique($blockedRoomIds);

        // If all rooms are blocked, treat as library closed
        if (count($blockedRoomIds) === $totalRooms) {
            $libraryClosedToday = true;
            $closureReason = "Semua ruangan diblokir"; // Default reason
        }
    }
    ?>

    <!-- Library Status Banner & Controls -->
    <div class="mb-6">
        <?php if ($libraryClosedToday): ?>
            <!-- Library CLOSED Status -->
            <div class="bg-red-50 border-2 border-red-300 rounded-2xl p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-red-900">Perpustakaan Sedang TUTUP</h3>
                            <p class="text-red-800 mt-1">
                                <strong>Alasan:</strong> <?= htmlspecialchars($closureReason ?? 'Tidak disebutkan') ?>
                            </p>
                            <p class="text-sm text-red-700 mt-2">
                                Semua pengguna tidak dapat membuat, reschedule, atau cancel booking saat ini.
                            </p>
                        </div>
                    </div>
                    <form action="/admin/blocked-dates/reopen-today" method="post" class="ml-4"
                        onsubmit="return confirm('Yakin ingin membuka perpustakaan kembali?');">
                        <?= csrf_field() ?>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                            Buka Perpustakaan
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Library OPEN - Show Close Button -->
            <div class="bg-emerald-50 border-2 border-emerald-300 rounded-2xl p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-bold text-emerald-900">Perpustakaan BUKA</h3>
                            <p class="text-emerald-800 mt-1">Pengguna dapat membuat booking secara normal.</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('closeTodayModal').classList.remove('hidden')"
                        class="ml-4 inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Tutup Perpustakaan HARI INI
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Close Library TODAY Modal -->
    <div id="closeTodayModal"
        class="hidden fixed inset-0 bg-black/50 bg-opacity-50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Tutup Perpustakaan Hari Ini</h3>
            <form action="/admin/blocked-dates/close-today" method="post">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Alasan Penutupan *</label>
                    <textarea name="alasan" rows="3" required minlength="5"
                        placeholder="Contoh: Mati listrik mendadak, Kegiatan darurat, dll."
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all resize-none"></textarea>
                    <p class="text-xs text-slate-500 mt-1">Minimal 5 karakter</p>
                </div>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4">
                    <p class="text-sm text-yellow-800">
                        <strong>Peringatan:</strong> Semua booking di hari ini akan dibatalkan/dihapus dan notifikasi
                        akan dikirim.
                    </p>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('closeTodayModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 border-2 border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                        Tutup Perpustakaan
                    </button>
                </div>
            </form>
        </div>
    </div>

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

                <form action="/admin/blocked-dates/preview" method="post" class="space-y-4">
                    <?= csrf_field() ?>

                    <div>
                        <label
                            class="block text-sm font-semibold mb-2 <?= $validator?->hasError('tanggal_begin') ? 'text-red-700' : 'text-slate-700' ?>">Tanggal
                            Mulai</label>
                        <input type="date" name="tanggal_begin" required min="<?= date('Y-m-d') ?>"
                            value="<?= htmlspecialchars(old('tanggal_begin') ?? '') ?>"
                            class="w-full px-4 py-3 border-2 rounded-xl focus:outline-none focus:ring-2 transition-all <?= $validator?->hasError('tanggal_begin') ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-red-500 focus:ring-red-200' ?>">
                        <?php if ($validator?->hasError('tanggal_begin')): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <?= htmlspecialchars($validator->getFirstError('tanggal_begin')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold mb-2 <?= $validator?->hasError('tanggal_end') ? 'text-red-700' : 'text-slate-700' ?>">Tanggal
                            Selesai</label>
                        <input type="date" name="tanggal_end" required min="<?= date('Y-m-d') ?>"
                            value="<?= htmlspecialchars(old('tanggal_end') ?? '') ?>"
                            class="w-full px-4 py-3 border-2 rounded-xl focus:outline-none focus:ring-2 transition-all <?= $validator?->hasError('tanggal_end') ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-red-500 focus:ring-red-200' ?>">
                        <?php if ($validator?->hasError('tanggal_end')): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <?= htmlspecialchars($validator->getFirstError('tanggal_end')) ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-3">Ruangan yang Diblokir</label>

                        <!-- Select All / Deselect All -->
                        <div class="mb-3 flex gap-2">
                            <button type="button" id="selectAllRooms"
                                class="text-xs px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition-colors">
                                Pilih Semua
                            </button>
                            <button type="button" id="deselectAllRooms"
                                class="text-xs px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition-colors">
                                Batal Semua
                            </button>
                        </div>

                        <!-- Room Checkboxes -->
                        <div
                            class="max-h-48 overflow-y-auto border-2 border-gray-200 rounded-xl p-3 space-y-2 focus-within:border-red-500 focus-within:ring-2 focus-within:ring-red-200 transition-all">
                            <?php if (empty($rooms)): ?>
                                <p class="text-sm text-slate-500 italic">Tidak ada ruangan tersedia</p>
                            <?php else: ?>
                                <?php foreach ($rooms as $room): ?>
                                    <label
                                        class="flex items-center p-2 hover:bg-slate-50 rounded-lg cursor-pointer transition-colors">
                                        <input type="checkbox" name="ruangan_ids[]" value="<?= (int) $room->id_ruangan ?>"
                                            class="room-checkbox w-4 h-4 text-red-500 border-gray-300 rounded focus:ring-red-500 focus:ring-2 transition-all">
                                        <span class="ml-3 text-sm font-medium text-slate-700">
                                            <?= htmlspecialchars($room->nama_ruangan) ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">
                            <strong>Tidak pilih apapun</strong> = memblokir <strong>semua ruangan</strong>
                        </p>
                    </div>

                    <script>
                        // Select All functionality
                        document.getElementById('selectAllRooms')?.addEventListener('click', function () {
                            document.querySelectorAll('.room-checkbox').forEach(cb => cb.checked = true);
                        });

                        // Deselect All functionality
                        document.getElementById('deselectAllRooms')?.addEventListener('click', function () {
                            document.querySelectorAll('.room-checkbox').forEach(cb => cb.checked = false);
                        });
                    </script>

                    <div>
                        <label
                            class="block text-sm font-semibold mb-2 <?= $validator?->hasError('alasan') ? 'text-red-700' : 'text-slate-700' ?>">Alasan</label>
                        <textarea name="alasan" rows="3" placeholder="Contoh: Libur Nasional, Maintenance, dll."
                            class="w-full px-4 py-3 border-2 rounded-xl focus:outline-none focus:ring-2 transition-all resize-none <?= $validator?->hasError('alasan') ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 focus:border-red-500 focus:ring-red-200' ?>"><?= htmlspecialchars(old('alasan') ?? '') ?></textarea>
                        <?php if ($validator?->hasError('alasan')): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <?= htmlspecialchars($validator->getFirstError('alasan')) ?></p>
                        <?php endif; ?>
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
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-slate-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Daftar Tanggal Diblokir
                    </h2>
                    <?php if (!empty($blockedDates)): ?>
                        <button type="button" onclick="document.getElementById('deleteAllModal').classList.remove('hidden')"
                            class="inline-flex items-center px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 rounded-lg transition-colors border border-red-300">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus Semua
                        </button>
                    <?php endif; ?>
                </div>

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
                                                <span
                                                    class="text-slate-800"><?= htmlspecialchars($blocked['nama_ruangan'] ?? 'Unknown') ?></span>
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

    <!-- Delete All Modal -->
    <div id='deleteAllModal'
        class='hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4'>
        <div class='bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl'>
            <div class='flex items-start mb-4'>
                <div class='flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mr-4'>
                    <svg class='w-6 h-6 text-red-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                        <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2'
                            d='M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' />
                    </svg>
                </div>
                <div class='flex-1'>
                    <h3 class='text-xl font-bold text-slate-800 mb-2'>Hapus Semua Blokir?</h3>
                    <p class='text-slate-600 mb-4'>Tindakan ini akan menghapus <strong>semua tanggal yang
                            diblokir</strong> dari sistem.</p>
                    <div class='bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4'>
                        <p class='text-sm text-yellow-800'><strong>Peringatan:</strong> Ini akan menghapus
                            <?= count($blockedDates ?? []) ?> entri blokir.</p>
                    </div>
                </div>
            </div>
            <form action='/admin/blocked-dates/delete-all' method='post'>
                <?= csrf_field() ?>
                <div class='flex gap-3'>
                    <button type='button' onclick='document.getElementById("deleteAllModal").classList.add("hidden")'
                        class='flex-1 px-4 py-2 border-2 border-slate-300 text-slate-700 font-semibold rounded-lg hover:bg-slate-50 transition-colors'>Batal</button>
                    <button type='submit'
                        class='flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors'>Ya,
                        Hapus Semua</button>
                </div>
            </form>
        </div>
    </div>
</div>