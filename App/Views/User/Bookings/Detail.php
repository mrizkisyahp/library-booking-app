<?php

$statusColors = [
    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
    'verified' => 'bg-blue-100 text-blue-800 border-blue-200',
    'active' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
    'completed' => 'bg-gray-100 text-gray-800 border-gray-200',
    'cancelled' => 'bg-rose-100 text-rose-800 border-rose-200',
    'draft' => 'bg-slate-100 text-slate-800 border-slate-200',
];
?>

<div class="p-4 bg-white shadow-md w-full">
    <div class="flex items-center gap-4 py-4">
        <div class="flex items-center gap-4 ">
            <a href="/dashboard">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-chevron-left-icon lucide-chevron-left size-9">
                    <path d="m15 18-6-6 6-6" />
                </svg>
            </a>
            <span class="text-black font-bold text-4xl">
                Detail Booking
            </span>
        </div>
    </div>
</div>

<div class="p-6 bg-gray-200 pb-8 shadow-md">
    <?php if ($booking->status === 'draft'): ?>
        <div class="w-full mb-4">
            <div
                class="inline-block bg-yellow-300 border-2 border-yellow-600 font-regular text-sm text-black w-full px-6 py-4 rounded-3xl text-left mb-4 font-regular tracking-wide">
                <p class="text-2xl font-bold uppercase my-4">
                    Peringatan!
                </p>
                <p class="mb-4">
                    Sistem tidak menahan jadwal untuk status Draft. Pengguna lain masih dapat membuat booking pada jam ini.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($rescheduleRequest): ?>
        <div class="w-full mb-4">
            <div class="bg-amber-100 border-2 border-amber-400 rounded-3xl px-6 py-4">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-lg font-bold text-amber-800">Permintaan Reschedule Pending</p>
                </div>
                <p class="text-sm text-amber-700 mb-2">Anda telah mengajukan perubahan jadwal. Menunggu persetujuan admin.
                </p>
                <div class="bg-white rounded-xl p-3 text-sm">
                    <p class="text-slate-600">Jadwal yang diajukan:</p>
                    <p class="font-bold text-slate-800">
                        <?= date('l, d F Y', strtotime($rescheduleRequest->requested_tanggal)) ?> |
                        <?= substr($rescheduleRequest->requested_waktu_mulai, 0, 5) ?> -
                        <?= substr($rescheduleRequest->requested_waktu_selesai, 0, 5) ?>
                    </p>
                </div>
            </div>
        </div>
    <?php elseif ($booking->has_been_rescheduled): ?>
        <div class="w-full mb-4">
            <div class="bg-slate-100 border-2 border-slate-300 rounded-3xl px-6 py-4">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-slate-600">Booking ini di-reschedule!</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="rounded-3xl border border-gray-200 bg-white shadow-md mb-6 max-w-7xl mx-auto">
        <div class="flex flex-col justify-start p-6">
            <p class="font-bold text-2xl mb-2">
                <?= htmlspecialchars($booking->nama_ruangan) ?>
            </p>
            <p class="mb-2">
                <?= htmlspecialchars($booking->jenis_ruangan) ?>
            </p>
            <div class="w-full">

                <p class="mb-4 flex gap-2 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-calendar-days-icon lucide-calendar-days size-4">
                        <path d="M8 2v4" />
                        <path d="M16 2v4" />
                        <rect width="18" height="18" x="3" y="4" rx="2" />
                        <path d="M3 10h18" />
                        <path d="M8 14h.01" />
                        <path d="M12 14h.01" />
                        <path d="M16 14h.01" />
                        <path d="M8 18h.01" />
                        <path d="M12 18h.01" />
                        <path d="M16 18h.01" />
                    </svg>
                    <?= date('l, d F Y', strtotime($booking->tanggal_penggunaan_ruang)) ?>
                </p>

                <p class="mb-4 flex gap-2 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-clock3-icon lucide-clock-3 size-4">
                        <path d="M12 6v6h4" />
                        <circle cx="12" cy="12" r="10" />
                    </svg>
                    <?= htmlspecialchars(substr($booking->waktu_mulai, 0, 5)) ?> -
                    <?= htmlspecialchars(substr($booking->waktu_selesai, 0, 5)) ?>
                </p>

                <p class="mb-4 flex gap-2 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-users-round-icon lucide-users-round size-4">
                        <path d="M18 21a8 8 0 0 0-16 0" />
                        <circle cx="10" cy="8" r="5" />
                        <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3" />
                    </svg>
                    <?= (int) $booking->current_members ?> /
                    <?= isset($booking->maximum_members) && $booking->maximum_members > 0 ? (int) $booking->maximum_members : '∞' ?>
                    peserta
                    <?php if (isset($booking->required_members) && $booking->required_members > 0): ?>
                        · Min <?= (int) $booking->required_members ?>
                    <?php endif; ?>
                </p>

                <p class="mb-4 flex gap-2 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-clock3-icon lucide-clock-3 size-4">
                        <path d="M12 6v6h4" />
                        <circle cx="12" cy="12" r="10" />
                    </svg>
                    <?= htmlspecialchars($booking->tujuan) ?>
                </p>

                <?php
                $statusColors = [
                    'draft' => 'bg-gray-300 text-gray-800 border-gray-400',
                    'pending' => 'bg-yellow-300 text-yellow-800 border-yellow-400',
                    'verified' => 'bg-blue-100 text-blue-800 border-blue-400',
                    'active' => 'bg-emerald-100 text-emerald-800 border-emerald-400',
                    'completed' => 'bg-green-100 text-green-800 border-green-400',
                    'cancelled' => 'bg-red-100 text-red-800 border-red-400',
                    'expired' => 'bg-slate-100 text-slate-700 border-slate-400',
                    'no_show' => 'bg-orange-100 text-orange-800 border-orange-400',
                ];
                $statusKey = strtolower($booking->status);
                $statusColor = $statusColors[$statusKey] ?? 'bg-gray-100 text-gray-800';
                $statusLabel = ucwords(str_replace('_', ' ', $statusKey));
                ?>
                <div class="px-4 py-2 mb-4 rounded-3xl font-regular border text-sm <?= $statusColor ?>">
                    Status:
                    <?= htmlspecialchars($statusLabel) ?>
                    <!-- status state -->
                    <?php if ($booking->status === 'draft' && $booking->current_members < $booking->required_members): ?>
                        (Menunggu Anggota)
                    <?php elseif ($booking->status === 'draft' && $booking->current_members >= $booking->required_members): ?>
                        (Siap Dikirim)
                    <?php elseif ($booking->status === 'pending'): ?>
                        (Menunggu Konfirmasi)
                    <?php elseif ($booking->status === 'verified'): ?>
                        (Terkonfirmasi)
                    <?php elseif ($booking->status === 'active'): ?>
                        (Sedang berlangsung)
                    <?php elseif ($booking->status === 'completed'): ?>
                        (Selesai)
                    <?php elseif ($booking->status === 'cancelled'): ?>
                        (Dibatalkan)
                    <?php endif ?>
                </div>
                <?php if ($booking->status === 'cancelled' && !empty($booking->alasan_reject)): ?>
                    <div class="w-full mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <p class="text-xs font-semibold text-red-700 uppercase mb-1">Alasan Pembatalan</p>
                        <p class="text-sm text-red-800"><?= htmlspecialchars($booking->alasan_reject) ?></p>
                    </div>
                <?php endif; ?>

                <div class="w-full mb-4">
                    <?php if ($isPic && $statusKey === 'completed' && empty($booking->id_feedback)): ?>
                        <a href="/feedback/create?booking=<?= (int) $booking->id_booking ?>"
                            class="inline-block text-emerald-600 hover:text-emerald-700 font-regular text-sm active:text-emerald-800 w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide underline focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                            Isi Feedback
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($booking->status === 'draft'): ?>
                    <hr class="h-px py-4 text-gray-400">
                    <p class="font-bold text-4xl mb-6">
                        Undang Anggota
                    </p>
                    <div class="bg-gray-200 rounded-lg p-3 mb-6 border border-gray-400 flex justify-between items-center">
                        <p class="font-medium tracking-[0.4rem] px-2 text-black break-all" id="inviteToken">
                            <?= htmlspecialchars($booking->invite_token) ?>
                        </p>
                        <div onclick="copyToken()"
                            class="relative p-2 cursor-pointer rounded-full hover:bg-emerald-50 hover:text-emerald-700 hover:border hover:border-emerald-700 active:text-emerald-700 active:border active:border-emerald-700 text-center transition-all">

                            <span id="copyToast"
                                class="absolute -top-8 right-0 text-xs bg-emerald-600 text-white px-2 py-1 rounded-md opacity-0 pointer-events-none transition-all duration-300">
                                Copied!
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-copy-icon lucide-copy size-4">
                                <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                            </svg>
                        </div>
                    </div>
                    <div class="w-full mb-4">
                        <a href="/bookings/draft?id=<?= (int) $booking->id_booking ?>"
                            class="inline-block bg-primary hover:bg-emerald-700 font-regular text-sm text-white w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all capitalize">
                            Bagikan tautan undangan
                        </a>
                    </div>
                <?php endif; ?>

                <hr class="h-px py-4 text-gray-400">
                <p class="font-bold text-4xl mb-6">
                    Anggota Booking
                </p>
                <p class="mb-4 flex gap-2 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-users-round-icon lucide-users-round size-4">
                        <path d="M18 21a8 8 0 0 0-16 0" />
                        <circle cx="10" cy="8" r="5" />
                        <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3" />
                    </svg>
                    <span>
                        <?= $booking->current_members . ' / ' . $booking->maximum_members ?> Orang
                    </span>
                </p>

                <?php if (isset($booking->maximum_members) && $booking->maximum_members > 0 && $booking->current_members >= $booking->maximum_members): ?>
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-red-800">Kapasitas Penuh</p>
                                <p class="text-sm text-red-700 mt-1">
                                    Ruangan sudah mencapai kapasitas maksimum. Anda tidak bisa menambah anggota lagi.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($allMembers->items)): ?>
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="text-slate-600">Belum ada anggota yang bergabung</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-6">
                        <?php foreach ($allMembers->items as $member): ?>
                            <div class="flex items-center p-3 bg-slate-50 rounded-xl">
                                <!-- <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div> -->
                                <div class="flex flex-1 min-w-0 items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-slate-800 truncate capitalize">
                                            <?= htmlspecialchars($member['nama'] ?? 'Unknown') ?>
                                        </p>
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
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($allMembers->lastPage > 1): ?>
                        <?php
                        $pagination = $allMembers;
                        $paginationQuery = $_GET;
                        $paginationQuery['id'] = $booking->id_booking;
                        ?>
                        <div class="bg-white rounded-2xl shadow-lg p-6 mt-6">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <p class="text-sm text-slate-600">
                                    Menampilkan <span
                                        class="font-semibold text-slate-800"><?= (($pagination->currentPage - 1) * $pagination->perPage) + 1 ?></span>
                                    sampai <span
                                        class="font-semibold text-slate-800"><?= min($pagination->currentPage * $pagination->perPage, $pagination->total) ?></span>
                                    dari <span class="font-semibold text-slate-800"><?= $pagination->total ?></span> anggota
                                </p>
                                <div class="flex gap-2 items-center">
                                    <!-- First Page -->
                                    <?php if ($pagination->currentPage > 1): ?>
                                        <?php $paginationQuery['page'] = 1; ?>
                                        <a href="/bookings/detail?<?= http_build_query($paginationQuery) ?>"
                                            class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                            Awal
                                        </a>
                                    <?php endif; ?>
                                    <!-- Previous -->
                                    <?php if ($pagination->currentPage > 1): ?>
                                        <?php $paginationQuery['page'] = $pagination->currentPage - 1; ?>
                                        <a href="/bookings/detail?<?= http_build_query($paginationQuery) ?>"
                                            class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                            ← Sebelumnya
                                        </a>
                                    <?php endif; ?>
                                    <!-- Page Numbers -->
                                    <div class="flex gap-1">
                                        <?php for ($i = 1; $i <= $pagination->lastPage; $i++): ?>
                                            <?php $paginationQuery['page'] = $i; ?>
                                            <a href="/bookings/detail?<?= http_build_query($paginationQuery) ?>" class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-semibold transition-all
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
                                        <a href="/bookings/detail?<?= http_build_query($paginationQuery) ?>"
                                            class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                            Selanjutnya →
                                        </a>
                                    <?php endif; ?>
                                    <!-- Last Page -->
                                    <?php if ($pagination->currentPage < $pagination->lastPage): ?>
                                        <?php $paginationQuery['page'] = $pagination->lastPage; ?>
                                        <a href="/bookings/detail?<?= http_build_query($paginationQuery) ?>"
                                            class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                            Akhir
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- BELOM KELAR -->
            </div>
        </div>
    </div>

    <!-- Info status desktop -->
    <div class="w-full mb-8 hidden md:block">
        <div class="flex items-center justify-around w-full px-4">
            <?php if ($booking->status === 'draft'): ?>
                <form action="/bookings/submit" method="post"
                    onsubmit="return confirm('Anda yakin ingin mengirimkan pengajuan ini?')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">

                    <button type="submit" <?= ($booking->status !== 'draft' || !$canSubmit) ? 'disabled' : '' ?>
                        class="w-full bg-primary text-white px-8 py-4 rounded-xl border hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl disabled:border-slate-400 disabled:bg-slate-200 disabled:cursor-not-allowed disabled:hover:shadow-lg disabled:text-slate-500 flex items-center justify-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        <?= $canSubmit ? 'Kirim ke Admin' : 'Lengkapi Anggota Terlebih Dahulu' ?>
                    </button>
                </form>
                <a href="/bookings/draft?id=<?= (int) $booking->id_booking ?>"
                    class="inline-block bg-red-600 hover:bg-red-700 font-regular text-sm text-white w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all capitalize">
                    Hapus draft booking
                </a>
            <?php endif; ?>
            <?php if ($booking->status === 'pending'): ?>
                <!-- button modal batalkan draft booking -->
                <a href="/bookings/detail?id=<?= (int) $booking->id_booking ?>#modal-cancel-draft"
                    class="w-fit bg-red-600 text-white px-8 py-4 rounded-xl hover:bg-red-700 transition-all font-semibold shadow-lg hover:shadow-xl flex items-center justify-center gap-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-circle-x-icon lucide-circle-x size-6">
                        <circle cx="12" cy="12" r="10" />
                        <path d="m15 9-6 6" />
                        <path d="m9 9 6 6" />
                    </svg>
                    Batalkan pengajuan draft
                </a>

                <!-- modal menu batalkan draft booking-->
                <div id="modal-cancel-draft"
                    class="fixed inset-0 bg-black/50 opacity-0 pointer-events-none duration-300 transition-all target:opacity-100 target:pointer-events-auto flex justify-center items-center z-999 backdrop-blur-xs">

                    <div
                        class="bg-white p-6 rounded-2xl w-11/12 max-w-md shadow-lg scale-95 transition-all duration-300 target:scale-100 relative">

                        <h1 class="text-4xl font-bold text-slate-800 mb-2">
                            Peringatan
                        </h1>
                        <p class="text-sm text-slate-600 mb-4">
                            Apakah Kamu yakin untuk menghapus Draft?
                        </p>

                        <form action="/bookings/cancel-pending" method="post" class="space-y-3">
                            <?= csrf_field() ?>

                            <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">

                            <div class="flex items-center gap-4 mt-6 text-center">
                                <a href="/bookings/detail?id=<?= (int) $booking->id_booking ?>"
                                    class="w-full bg-slate-200 text-black p-4 rounded-2xl hover:bg-slate-300 transition-all font-regular border border-slate-400 shadow cursor-pointer">
                                    Tidak
                                </a>

                                <button type="submit"
                                    class="w-full bg-rose-500 text-white p-4 rounded-2xl hover:bg-rose-600 transition-all font-regular border border-rose-700 shadow cursor-pointer">
                                    Ya, Hapus
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($booking->status === 'verified'): ?>
                <div class="text-2xl font-bold mb-4">
                    Kode Kedatangan
                </div>
                <div>
                    <span>Berlaku sampai </span>
                    <span><?= htmlspecialchars($booking->waktu_mulai) ?></span>
                </div>
                <div class="bg-gray-200 rounded-lg p-3 mb-6 border border-gray-400 flex justify-between items-center">
                    <p class="font-medium tracking-[0.4rem] px-2 text-black break-all" id="checkinCode">
                        <?= htmlspecialchars($booking->checkin_code) ?>
                    </p>
                    <div onclick="copyToken()"
                        class="relative p-2 cursor-pointer rounded-full hover:bg-emerald-50 hover:text-emerald-700 hover:border hover:border-emerald-700 active:text-emerald-700 active:border active:border-emerald-700 text-center transition-all">

                        <span id="copyToast"
                            class="absolute -top-8 right-0 text-xs bg-emerald-600 text-white px-2 py-1 rounded-md opacity-0 pointer-events-none transition-all duration-300">
                            Copied!
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-copy-icon lucide-copy size-4">
                            <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                            <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                        </svg>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($booking->status === 'active'): ?>
                <div class="text-2xl font-bold mb-4">
                    Waktu Tersisa
                </div>
                <div class="bg-gray-200 rounded-lg p-3 mb-6 border border-gray-400 flex justify-between items-center">
                    <div class="text-2xl font-bold" id="countdown-timer"
                        data-end-time="<?= date('Y-m-d H:i:s', strtotime($booking->tanggal_penggunaan_ruang . ' ' . $booking->waktu_selesai)) ?>">
                        00:00:00
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($booking->status === 'completed' && empty($booking->id_feedback)): ?>
                <a href="/feedback/create?booking=<?= (int) $booking->id_booking ?>"
                    class="inline-block bg-primary hover:bg-emerald-700 font-regular text-white active:bg-emerald-800 w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                    Isi Feedback (Wajib)
                </a>
            <?php endif ?>
            <?php if ($booking->status === 'completed' && $booking->id_feedback): ?>
                <div
                    class="bg-gray-200 rounded-lg p-3 mb-4 border text-gray-800 border-gray-400 flex justify-between items-center">
                    Seluruh rangkaian booking sudah diselesaikan
                </div>
            <?php endif ?>
        </div>

        <?php if ($isPic && $booking->status === 'verified'): ?>
            <div class="bg-white rounded-2xl shadow-lg p-8 space-y-4">
                <h3 class="text-xl font-bold text-slate-800 mb-4">Aksi</h3>

                <!-- Reschedule Button -->
                <?php if (!$rescheduleRequest && !$booking->has_been_rescheduled): ?>
                    <a href="/bookings/reschedule?id=<?= (int) $booking->id_booking ?>"
                        class="w-full bg-amber-500 text-white px-8 py-4 rounded-xl hover:bg-amber-600 transition-all font-semibold shadow-lg flex items-center justify-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reschedule Booking
                    </a>
                <?php elseif ($rescheduleRequest): ?>
                    <div
                        class="w-full bg-amber-200 text-amber-700 px-8 py-4 rounded-xl font-semibold shadow-lg flex items-center justify-center cursor-not-allowed">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Menunggu Persetujuan Reschedule
                    </div>
                <?php elseif ($booking->has_been_rescheduled): ?>
                    <div
                        class="w-full bg-slate-300 text-slate-500 px-8 py-4 rounded-xl font-semibold shadow-lg flex items-center justify-center cursor-not-allowed">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Sudah Pernah Reschedule
                    </div>
                <?php endif; ?>

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

    <!-- nav overlay mobile -->
    <nav class="fixed left-0 bottom-0 right-0 bg-gray-100 text-white md:hidden z-999 rounded-t-4xl py-6 shadow-xl">
        <div class="flex items-center justify-around w-full px-4">
            <?php if ($booking->status === 'draft'): ?>
                <form action="/bookings/submit" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">

                    <button type="submit" <?= ($booking->status !== 'draft' || !$canSubmit) ? 'disabled' : '' ?>
                        class="w-full bg-primary text-white px-8 py-4 rounded-xl border hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl disabled:border-slate-400 disabled:bg-slate-200 disabled:cursor-not-allowed disabled:hover:shadow-lg disabled:text-slate-500 flex items-center justify-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        <?= $canSubmit ? 'Kirim ke Admin' : 'Lengkapi Anggota Terlebih Dahulu' ?>
                    </button>
                </form>
            <?php endif; ?>

            <?php if ($booking->status === 'pending'): ?>

                <!-- button modal batalkan draft booking -->
                <a href="/bookings/detail?id=<?= (int) $booking->id_booking ?>#modal-cancel-draft-mobile"
                    class="w-full bg-red-600 text-white px-8 py-4 rounded-xl hover:bg-red-700 transition-all font-semibold shadow-lg hover:shadow-xl flex items-center justify-center gap-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-circle-x-icon lucide-circle-x size-6">
                        <circle cx="12" cy="12" r="10" />
                        <path d="m15 9-6 6" />
                        <path d="m9 9 6 6" />
                    </svg>
                    Batalkan pengajuan draft
                </a>


                <!-- modal menu batalkan draft booking-->
                <div id="modal-cancel-draft-mobile"
                    class="fixed inset-0 bg-black/50 opacity-0 pointer-events-none duration-300 transition-all target:opacity-100 target:pointer-events-auto flex justify-center items-center z-999 backdrop-blur-xs">

                    <div
                        class="bg-white p-6 rounded-2xl w-11/12 max-w-md shadow-lg scale-95 transition-all duration-300 target:scale-100 relative">

                        <h1 class="text-4xl font-bold text-slate-800 mb-2">
                            Peringatan
                        </h1>
                        <p class="text-sm text-slate-600 mb-4">
                            Apakah Kamu yakin untuk menghapus Draft?
                        </p>

                        <form action="/bookings/cancel-pending" method="post" class="space-y-3">
                            <?= csrf_field() ?>

                            <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">

                            <div class="flex items-center gap-4 mt-6 text-center">
                                <a href="/bookings/detail?id=<?= (int) $booking->id_booking ?>"
                                    class="w-full bg-slate-200 text-black p-4 rounded-2xl hover:bg-slate-300 transition-all font-regular border border-slate-400 shadow cursor-pointer">
                                    Tidak
                                </a>

                                <button type="submit"
                                    class="w-full bg-rose-500 text-white p-4 rounded-2xl hover:bg-rose-600 transition-all font-regular border border-rose-700 shadow cursor-pointer">
                                    Ya, Hapus
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            <?php endif; ?>


            <?php if ($booking->status === 'verified'): ?>
                <div class="text-2xl font-bold mb-4">
                    Kode Kedatangan
                </div>
                <div>
                    <span>Berlaku sampai </span>
                    <span><?= htmlspecialchars($booking->waktu_mulai + 15) ?></span>
                </div>
                <div class="bg-gray-200 rounded-lg p-3 mb-6 border border-gray-400 flex justify-between items-center">
                    <p class="font-medium tracking-[0.4rem] px-2 text-black break-all" id="checkinCode">
                        <?= htmlspecialchars($booking->checkin_code) ?>
                    </p>
                    <div onclick="copyToken()"
                        class="relative p-2 cursor-pointer rounded-full hover:bg-emerald-50 hover:text-emerald-700 hover:border hover:border-emerald-700 active:text-emerald-700 active:border active:border-emerald-700 text-center transition-all">

                        <span id="copyToast"
                            class="absolute -top-8 right-0 text-xs bg-emerald-600 text-white px-2 py-1 rounded-md opacity-0 pointer-events-none transition-all duration-300">
                            Copied!
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-copy-icon lucide-copy size-4">
                            <rect width="14" height="14" x="8" y="8" rx="2" ry="2" />
                            <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2" />
                        </svg>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($booking->status === 'active'): ?>
                <div class="text-2xl font-bold mb-4">
                    Waktu Tersisa
                </div>
                <div class="bg-gray-200 rounded-lg p-3 mb-6 border border-gray-400 flex justify-between items-center">
                    <div class="text-2xl font-bold" id="countdown-timer-mobile"
                        data-end-time="<?= date('Y-m-d H:i:s', strtotime($booking->tanggal_penggunaan_ruang . ' ' . $booking->waktu_selesai)) ?>">
                        00:00:00
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($booking->status === 'completed' && $booking->feedback() === null): ?>
                <a href="/feedback/create?booking=<?= (int) $booking->id_booking ?>"
                    class="inline-block bg-primary hover:bg-emerald-700 font-regular text-white active:bg-emerald-800 w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                    Isi Feedback (Wajib)
                </a>
            <?php endif; ?>

            <?php if ($booking->status === 'cancelled'): ?>
                <div class="w-full">
                    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 mb-4">
                        <p class="font-semibold text-red-800">Booking telah dibatalkan</p>
                        <?php if (!empty($booking->alasan_reject)): ?>
                            <div class="mt-2 text-sm text-red-700">
                                <p class="font-medium">Alasan:</p>
                                <p><?= htmlspecialchars($booking->alasan_reject) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($booking->status === 'completed' && $booking->id_feedback): ?>
                <div
                    class="bg-gray-200 rounded-lg p-3 mb-4 border text-gray-800 border-gray-400 flex justify-between items-center">
                    Seluruh rangkaian booking sudah diselesaikan
                </div>
            <?php endif; ?>

            <?php if ($booking->status === 'completed'): ?>
                <div
                    class="bg-gray-200 rounded-lg p-3 mb-4 border text-gray-800 border-gray-400 flex justify-between items-center">
                    Seluruh rangkaian booking sudah diselesaikan
                </div>
            <?php endif; ?>
        </div>
    </nav>

</div>

<!-- Modal Waktu Habis -->
<div id="modal-waktu-habis"
    class="fixed inset-0 bg-black/50 opacity-0 pointer-events-none duration-300 transition-all flex justify-center items-center z-[9999] backdrop-blur-sm">
    <div class="bg-white p-8 rounded-2xl w-11/12 max-w-md shadow-lg scale-95 transition-all duration-300 relative">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg class="h-10 w-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-slate-800 mb-2">
                Waktu Habis
            </h1>
            <p class="text-sm text-slate-600 mb-6">
                Waktu booking Anda telah berakhir. Silakan refresh halaman untuk melihat status terbaru.
            </p>
            <button onclick="location.reload()"
                class="w-full bg-primary text-white p-4 rounded-2xl hover:bg-emerald-700 transition-all font-semibold shadow cursor-pointer">
                Refresh Halaman
            </button>
        </div>
    </div>
</div>

<?php if ($booking->status === 'active'): ?>
    <script>
        function startCountdown(elementId) {
            const timerElement = document.getElementById(elementId);
            if (!timerElement) return;

            const endTime = new Date(timerElement.getAttribute('data-end-time')).getTime();

            function updateTimer() {
                const now = new Date().getTime();
                const distance = endTime - now;

                if (distance < 0) {
                    timerElement.textContent = '00:00:00';
                    showWaktuHabisModal();
                    return;
                }

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                timerElement.textContent =
                    String(hours).padStart(2, '0') + ':' +
                    String(minutes).padStart(2, '0') + ':' +
                    String(seconds).padStart(2, '0');

                setTimeout(updateTimer, 1000);
            }

            updateTimer();
        }

        function showWaktuHabisModal() {
            const modal = document.getElementById('modal-waktu-habis');
            if (modal) {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                modal.classList.add('opacity-100', 'pointer-events-auto');
                modal.querySelector('div > div').classList.remove('scale-95');
                modal.querySelector('div > div').classList.add('scale-100');
            }
        }

        // Start countdown for both desktop and mobile timers
        startCountdown('countdown-timer');
        startCountdown('countdown-timer-mobile');
    </script>
<?php endif; ?>
