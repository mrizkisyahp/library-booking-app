<div class="max-w-5xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="/my-bookings"
            class="inline-flex items-center text-emerald-600 hover:text-emerald-700 font-semibold group">
            <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke My Bookings
        </a>
    </div>

    <!-- Page Header -->
    <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-3xl font-bold text-slate-800 flex items-center">
                <svg class="w-8 h-8 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Detail Booking
            </h2>
            <?php
            $statusColors = [
                'draft' => 'bg-gray-100 text-gray-800 border-gray-300',
                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                'verified' => 'bg-green-100 text-green-800 border-green-300',
                'active' => 'bg-blue-100 text-blue-800 border-blue-300',
                'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                'cancelled' => 'bg-red-100 text-red-800 border-red-300',
                'rejected' => 'bg-red-100 text-red-800 border-red-300',
                'no_show' => 'bg-orange-100 text-orange-800 border-orange-300',
            ];
            $statusClass = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800 border-gray-300';
            ?>
            <span class="inline-flex px-4 py-2 rounded-lg font-semibold text-sm border-2 <?= $statusClass ?>">
                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $booking->status))) ?>
            </span>
        </div>
        <p class="text-slate-600">Booking Code: <span
                class="font-mono font-bold"><?= htmlspecialchars($booking->booking_code ?? '-') ?></span></p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Booking Details -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Detail Booking
                </h3>

                <div class="space-y-4">
                    <div class="flex items-start p-4 bg-slate-50 rounded-xl">
                        <svg class="w-5 h-5 mr-3 text-emerald-600 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-slate-600">Ruangan</p>
                            <p class="text-lg font-bold text-slate-800"><?= htmlspecialchars($booking->nama_ruangan) ?>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start p-4 bg-slate-50 rounded-xl">
                        <svg class="w-5 h-5 mr-3 text-emerald-600 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-slate-600">Tanggal Penggunaan</p>
                            <p class="text-lg font-bold text-slate-800">
                                <?= date('l, d F Y', strtotime($booking->tanggal_penggunaan_ruang)) ?>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start p-4 bg-slate-50 rounded-xl">
                        <svg class="w-5 h-5 mr-3 text-emerald-600 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-slate-600">Waktu</p>
                            <p class="text-lg font-bold text-slate-800">
                                <?= htmlspecialchars(substr($booking->waktu_mulai, 0, 5)) ?> -
                                <?= htmlspecialchars(substr($booking->waktu_selesai, 0, 5)) ?>
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($booking->tujuan)): ?>
                        <div class="flex items-start p-4 bg-slate-50 rounded-xl">
                            <svg class="w-5 h-5 mr-3 text-emerald-600 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-slate-600">Tujuan</p>
                                <p class="text-lg font-bold text-slate-800"><?= htmlspecialchars($booking->tujuan) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Check-in Section (verified status only) -->
            <?php if ($booking->status === 'verified' && !empty($booking->checkin_code)): ?>
                <div
                    class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl shadow-lg p-8 border-2 border-emerald-300">
                    <h3 class="text-xl font-bold text-emerald-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Kode Check-in
                    </h3>
                    <p class="text-sm text-emerald-700 mb-4">Tunjukkan kode ini saat check-in di lokasi</p>
                    <div class="bg-white rounded-xl p-6 text-center border-2 border-emerald-300">
                        <p class="text-4xl font-bold font-mono text-emerald-700 tracking-widest">
                            <?= htmlspecialchars($booking->checkin_code) ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Members Section -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Daftar Anggota
                    </h3>
                    <div class="text-sm font-semibold px-3 py-1 rounded-full bg-emerald-100 text-emerald-700">
                        <?= (int) $booking->current_members ?> /
                        <?= $booking->maximum_members > 0 ? (int) $booking->maximum_members : '∞' ?> peserta
                    </div>
                </div>

                <?php if (empty($allMembers)): ?>
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="text-slate-600">Belum ada anggota yang bergabung</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php foreach ($allMembers as $member): ?>
                            <div class="flex items-center p-3 bg-slate-50 rounded-xl">
                                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-800 truncate">
                                        <?= htmlspecialchars($member['nama'] ?? 'Unknown') ?></p>
                                    <p class="text-sm text-slate-500 truncate"><?= htmlspecialchars($member['email']) ?></p>
                                    <?php if (!empty($member['is_owner'])): ?>
                                        <span class="text-xs text-emerald-600 font-semibold flex items-center mt-1">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            PIC
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Actions Section (PIC only, verified status only) -->
            <?php if ($isPic && $booking->status === 'verified'): ?>
                <div class="bg-white rounded-2xl shadow-lg p-8 space-y-4">
                    <h3 class="text-xl font-bold text-slate-800 mb-4">Aksi</h3>

                    <!-- Reschedule Button -->
                    <a href="/bookings/reschedule?id=<?= (int) $booking->id_booking ?>"
                        class="w-full bg-amber-500 text-white px-8 py-4 rounded-xl hover:bg-amber-600 transition-all font-semibold shadow-lg flex items-center justify-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reschedule Booking
                    </a>

                    <!-- Cancel Booking -->
                    <form action="/bookings/cancel" method="post"
                        onsubmit="return confirm('Yakin ingin membatalkan booking ini? Semua anggota akan dikeluarkan.');">
                        <?= csrf_field() ?>
                        <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
                        <button type="submit"
                            class="w-full bg-red-500 text-white px-8 py-4 rounded-xl hover:bg-red-600 transition-all font-semibold shadow-lg flex items-center justify-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Batalkan Booking
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- PIC Info Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="font-bold text-slate-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    PIC (Penanggung Jawab)
                </h3>
                <div class="bg-slate-50 rounded-xl p-4">
                    <p class="font-semibold text-slate-800"><?= htmlspecialchars($pic['nama'] ?? 'Unknown') ?></p>
                    <p class="text-sm text-slate-500"><?= htmlspecialchars($pic['email'] ?? '-') ?></p>
                </div>
            </div>

            <!-- Status Info Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="font-bold text-slate-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Status Booking
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Status</span>
                        <span
                            class="font-semibold <?= $booking->status === 'verified' ? 'text-green-600' : 'text-slate-800' ?>">
                            <?= ucfirst(str_replace('_', ' ', $booking->status)) ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Dibuat</span>
                        <span
                            class="font-semibold text-slate-800"><?= date('d M Y', strtotime($booking->tanggal_booking ?? $booking->created_at ?? 'now')) ?></span>
                    </div>
                    <?php if (!empty($booking->has_been_rescheduled) && $booking->has_been_rescheduled): ?>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Rescheduled</span>
                            <span class="font-semibold text-amber-600">Ya</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>