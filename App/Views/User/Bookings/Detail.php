<?php

use App\Core\Csrf;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;

/** @var Booking $booking */
/** @var Room|null $room */
/** @var User|null $pic */
$members = $members ?? [];

$statusColors = [
    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'verified' => 'bg-blue-100 text-blue-800 border-blue-200',
    'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
    'completed' => 'bg-gray-100 text-gray-800 border-gray-200',
    'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
    'draft' => 'bg-slate-100 text-slate-800 border-slate-200',
];

$roomName = $room?->nama_ruangan ?? ('Ruangan #' . htmlspecialchars((string) $booking->ruangan_id));
$picName = $pic?->nama ?? 'Pengguna #' . htmlspecialchars((string) $booking->user_id);
$picContact = $pic?->email ?? '-';
$picIdNumber = $pic?->nim ?: $pic?->nip ?: '-';
?>

<div class="max-w-5xl mx-auto space-y-6">
    <div class="mb-2">
        <a href="/bookings"
            class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold group">
            <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke daftar booking
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-2">
            <div>
                <p class="text-sm text-slate-500 uppercase">Detail Booking</p>
                <h1 class="text-3xl font-bold text-slate-800">#<?= htmlspecialchars((string) $booking->id_booking) ?>
                </h1>
            </div>
            <span
                class="inline-flex px-4 py-2 rounded-lg font-semibold text-sm border <?= $statusColors[$booking->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' ?>">
                <?= htmlspecialchars(ucfirst($booking->status)) ?>
            </span>
        </div>
        <p class="text-slate-600">Informasi detail mengenai booking anda.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-lg p-8 space-y-5">
                <h2 class="text-xl font-bold text-slate-800 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Ringkasan Booking
                </h2>

                <div class="space-y-4">
                    <div class="p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs font-semibold text-slate-500 uppercase">Ruangan</p>
                        <p class="text-lg font-bold text-slate-800"><?= htmlspecialchars($roomName) ?></p>
                        <?php if ($room): ?>
                            <p class="text-sm text-slate-600">Kapasitas:
                                <?= htmlspecialchars((string) $room->kapasitas_min) ?> -
                                <?= htmlspecialchars((string) $room->kapasitas_max) ?> orang
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs font-semibold text-slate-500 uppercase">PIC</p>
                        <p class="text-lg font-bold text-slate-800"><?= htmlspecialchars($picName) ?></p>
                        <p class="text-sm text-slate-600"><?= htmlspecialchars($picContact) ?></p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 rounded-xl">
                            <p class="text-xs font-semibold text-slate-500 uppercase">Tanggal Penggunaan</p>
                            <p class="text-lg font-bold text-slate-800">
                                <?= date('l, d F Y', strtotime($booking->tanggal_penggunaan_ruang)) ?>
                            </p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl">
                            <p class="text-xs font-semibold text-slate-500 uppercase">Waktu</p>
                            <p class="text-lg font-bold text-slate-800">
                                <?= htmlspecialchars(substr($booking->waktu_mulai, 0, 5)) ?> -
                                <?= htmlspecialchars(substr($booking->waktu_selesai, 0, 5)) ?>
                            </p>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl">
                        <p class="text-xs font-semibold text-slate-500 uppercase">Tujuan</p>
                        <p class="text-sm text-slate-700"><?= nl2br(htmlspecialchars($booking->tujuan ?? '-')) ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Anggota
                    </h3>
                    <span class="text-sm text-slate-500"><?= count($members) ?> anggota terdaftar</span>
                </div>

                <?php if (empty($members)): ?>
                    <p class="text-sm text-slate-500">Belum ada anggota yang ditambahkan.</p>
                <?php else: ?>
                    <div class="overflow-hidden border border-slate-100 rounded-2xl">
                        <table class="w-full">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        Nama</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                <?php foreach ($members as $member): ?>
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-semibold text-slate-800">
                                            <?= htmlspecialchars($member['nama'] ?? '-') ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-slate-600">
                                            <?= htmlspecialchars($member['email'] ?? '-') ?></td>
                                        <td class="px-4 py-3 text-sm">
                                            <?php if (!empty($member['is_owner'])): ?>
                                                <span
                                                    class="px-3 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">PIC</span>
                                            <?php else: ?>
                                                <span
                                                    class="px-3 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-700">Anggota</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="space-y-6">
            <?php if ($booking->status === 'verified'): ?>
                <div class="bg-white rounded-2xl shadow-lg p-6 space-y-4">
                    <h3 class="text-lg font-bold text-slate-800">Kode Akses</h3>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Kode Check-in</p>
                        <div
                            class="p-4 bg-slate-100 rounded-xl font-mono tracking-widest text-center text-lg text-slate-800">
                            <?= $booking->checkin_code ? htmlspecialchars($booking->checkin_code) : 'Belum tersedia' ?>
                        </div>
                        <p class="text-xs text-slate-500 mt-2 text-center">Tunjukkan kode ini kepada admin untuk mengambil
                            kunci.</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Action Buttons could go here if needed, e.g. Cancel Booking -->
            <!-- For now, we only display info -->

        </div>
    </div>
</div>