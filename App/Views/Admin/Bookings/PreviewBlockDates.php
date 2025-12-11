<?php
use App\Core\App;
?>

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-800">Konfirmasi Blokir Tanggal</h1>
            <p class="text-slate-600">Review dampak sebelum memblokir tanggal</p>
        </div>
        <a href="/admin/blocked-dates"
            class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <!-- Summary Card -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-slate-800 mb-4">Ringkasan</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-slate-50 rounded-xl p-4">
                <div class="text-sm text-slate-600 mb-1">Periode</div>
                <div class="font-semibold text-slate-800">
                    <?= date('d M Y', strtotime($dateBegin)) ?> -
                    <?= date('d M Y', strtotime($dateEnd)) ?>
                </div>
            </div>

            <div class="bg-slate-50 rounded-xl p-4">
                <div class="text-sm text-slate-600 mb-1">Ruangan</div>
                <div class="font-semibold text-slate-800">
                    <?= implode(', ', $selectedRooms) ?>
                </div>
            </div>

            <div class="bg-slate-50 rounded-xl p-4">
                <div class="text-sm text-slate-600 mb-1">Booking Terdampak</div>
                <div class="font-semibold text-red-600 text-2xl">
                    <?= count($affectedBookings) ?>
                </div>
            </div>
        </div>

        <?php if (!empty($alasan)): ?>
            <div class="mt-4 bg-amber-50 rounded-xl p-4">
                <div class="text-sm text-amber-800 mb-1 font-semibold">Alasan:</div>
                <div class="text-amber-900"><?= htmlspecialchars($alasan) ?></div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Warning Box -->
    <?php if (!empty($affectedBookings)): ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-6 mb-6 rounded-lg">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0 mt-1" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-red-800 mb-2">Peringatan!</h3>
                    <p class="text-red-700">
                        <strong><?= count($affectedBookings) ?> booking aktif</strong> akan dibatalkan secara otomatis.
                        Email notifikasi akan dikirim ke setiap pengguna yang terdampak.
                    </p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="bg-green-50 border-l-4 border-green-500 p-6 mb-6 rounded-lg">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-green-800 mb-2">Aman</h3>
                    <p class="text-green-700">Tidak ada booking yang akan terdampak pada periode ini.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Affected Bookings List -->
    <?php if (!empty($affectedBookings)): ?>
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4">Booking yang Akan Dibatalkan</h2>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-slate-200">
                            <th class="text-left py-3 px-4 font-semibold text-slate-700">#</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-700">Tanggal</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-700">Waktu</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-700">Ruangan</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-700">Pengguna</th>
                            <th class="text-left py-3 px-4 font-semibold text-slate-700">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($affectedBookings as $index => $booking): ?>
                            <tr class="border-b border-slate-100 hover:bg-red-50">
                                <td class="py-3 px-4 text-slate-600"><?= $index + 1 ?></td>
                                <td class="py-3 px-4 font-medium text-slate-800">
                                    <?= date('d M Y', strtotime($booking['tanggal_penggunaan_ruang'])) ?>
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    <?= substr($booking['waktu_mulai'], 0, 5) ?> -
                                    <?= substr($booking['waktu_selesai'], 0, 5) ?>
                                </td>
                                <td class="py-3 px-4 text-slate-800">
                                    <?= htmlspecialchars($booking['nama_ruangan']) ?>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-slate-800"><?= htmlspecialchars($booking['user_nama']) ?></div>
                                    <div class="text-xs text-slate-500"><?= htmlspecialchars($booking['user_email']) ?></div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-semibold 
                                        <?php
                                        echo match ($booking['status']) {
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'verified' => 'bg-blue-100 text-blue-700',
                                            'active' => 'bg-green-100 text-green-700',
                                            default => 'bg-slate-100 text-slate-700'
                                        };
                                        ?>">
                                        <?= ucfirst($booking['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Confirmation Buttons -->
    <div class="flex gap-4 justify-end">
        <a href="/admin/blocked-dates"
            class="px-6 py-3 bg-slate-200 text-slate-700 rounded-xl hover:bg-slate-300 transition-all font-semibold">
            Batal
        </a>

        <form action="/admin/blocked-dates/confirm" method="post" class="inline">
            <?= csrf_field() ?>
            <input type="hidden" name="tanggal_begin" value="<?= htmlspecialchars($dateBegin) ?>">
            <input type="hidden" name="tanggal_end" value="<?= htmlspecialchars($dateEnd) ?>">
            <input type="hidden" name="alasan" value="<?= htmlspecialchars($alasan) ?>">

            <?php if (!empty($ruanganIds)): ?>
                <?php foreach ($ruanganIds as $roomId): ?>
                    <input type="hidden" name="ruangan_ids[]" value="<?= (int) $roomId ?>">
                <?php endforeach; ?>
            <?php endif; ?>

            <button type="submit" <?php if (!empty($affectedBookings)): ?>
                    onclick="return confirm('⚠️ KONFIRMASI: <?= count($affectedBookings) ?> booking akan dibatalkan dan email dikirim. Lanjutkan?')"
                <?php endif; ?>
                class="px-6 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all font-semibold flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Ya, Blokir Tanggal
            </button>
        </form>
    </div>
</div>