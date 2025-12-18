<!-- Welcome Header -->
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

<div class="p-6 bg-gray-200">
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
    <div class="rounded-3xl border border-gray-200 bg-white shadow-md mb-6">
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
                    <?php endif ?>
                </div>

                <?php if ($booking->status === 'draft' && $isPic): ?>
                    <div class="w-full mb-4">
                        <a href="/bookings/edit-draft?id=<?= (int) $booking->id_booking ?>"
                            class="inline-block bg-primary hover:bg-emerald-700 font-regular text-sm text-white w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all capitalize">
                            ubah rincian booking
                        </a>
                    <?php endif ?>
                    <?php if ($isPic && $statusKey === 'completed' && empty($booking->id_feedback)): ?>
                        <a href="/feedback/create?booking=<?= (int) $booking->id_booking ?>"
                            class="inline-block text-emerald-600 hover:text-emerald-700 font-regular text-sm active:text-emerald-800 w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide underline focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                            Isi Feedback
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($booking->status === 'draft' && $isPic): ?>
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
                <?php endif; ?>

                <hr class="h-px py-4 text-gray-400">
                <?php if ($isPic): ?>
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
                            <?= (int) $booking->current_members ?> /
                            <?= isset($booking->maximum_members) && $booking->maximum_members > 0 ? (int) $booking->maximum_members : '∞' ?>
                            peserta
                            <?php if (isset($booking->required_members) && $booking->required_members > 0): ?>
                                · Min <?= (int) $booking->required_members ?>
                            <?php endif; ?>
                            <?= isset($booking->maximum_members) && $booking->maximum_members > 0 ? (int) $booking->maximum_members : '∞' ?>
                            Orang
                        </span>
                    </p>
                <?php endif; ?>

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

                <?php if ($allMembers->total == 0): ?>
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
                                <div class="flex flex-1 min-w-0 items-center justify-start md:justify-between">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <p class="font-semibold text-slate-800 truncate capitalize ">
                                                <?= htmlspecialchars($member['nama'] ?? 'Unknown') ?>
                                            </p>
                                            <?php if (!empty($member['is_owner'])): ?>
                                                <span
                                                    class="block md:hidden text-xs text-primary font-semibold items-center border-emerald-700 ml-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-crown-icon lucide-crown size-4">
                                                        <path
                                                            d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z" />
                                                        <path d="M5 21h14" />
                                                    </svg>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-sm text-slate-500 truncate">
                                            <?= htmlspecialchars($member['email'] ?? '') ?></p>
                                    </div>

                                    <?php if (!empty($member['is_owner'])): ?>
                                        <span
                                            class="hidden md:block text-xs text-white font-semibold flex items-center mt-1 bg-emerald-600 p-2 rounded-md border border-emerald-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="lucide lucide-crown-icon lucide-crown size-4 md:size-6">
                                                <path
                                                    d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z" />
                                                <path d="M5 21h14" />
                                            </svg>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($isPic && empty($member['is_owner'])): ?>
                                    <form action="/bookings/kick" method="post" class="ml-2"
                                        onsubmit="return confirm('Keluarkan anggota ini?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
                                        <input type="hidden" name="user_id" value="<?= (int) $member['id_user'] ?>">
                                        <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Keluarkan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </form>
                                <?php endif; ?>
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
                                        <a href="/bookings/draft?<?= http_build_query($paginationQuery) ?>"
                                            class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                            Awal
                                        </a>
                                    <?php endif; ?>
                                    <!-- Previous -->
                                    <?php if ($pagination->currentPage > 1): ?>
                                        <?php $paginationQuery['page'] = $pagination->currentPage - 1; ?>
                                        <a href="/bookings/draft?<?= http_build_query($paginationQuery) ?>"
                                            class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                            ← Sebelumnya
                                        </a>
                                    <?php endif; ?>
                                    <!-- Page Numbers -->
                                    <div class="flex gap-1">
                                        <?php for ($i = 1; $i <= $pagination->lastPage; $i++): ?>
                                            <?php $paginationQuery['page'] = $i; ?>
                                            <a href="/bookings/draft?<?= http_build_query($paginationQuery) ?>" class="w-10 h-10 flex items-center justify-center rounded-xl text-sm font-semibold transition-all
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
                                        <a href="/bookings/draft?<?= http_build_query($paginationQuery) ?>"
                                            class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                            Selanjutnya →
                                        </a>
                                    <?php endif; ?>
                                    <!-- Last Page -->
                                    <?php if ($pagination->currentPage < $pagination->lastPage): ?>
                                        <?php $paginationQuery['page'] = $pagination->lastPage; ?>
                                        <a href="/bookings/draft?<?= http_build_query($paginationQuery) ?>"
                                            class="px-4 py-2 border-2 border-slate-300 rounded-xl text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                            Akhir
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($isPic): ?>
                    <!-- Add Member Form -->
                    <form action="/invitations/send" method="post" class="border-t pt-6">
                        <?= csrf_field() ?>
                        <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tambah Anggota</label>
                        <div class="flex gap-6 flex-col md:flex-row justify-between items-start md:items-center">
                            <input type="text" name="identifier" placeholder="NIM / NIP / Email"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all text-sm"
                                required>
                            <button type="submit"
                                class="bg-emerald-600 text-white px-6 py-3 rounded-xl hover:bg-emerald-700 transition-all font-semibold whitespace-nowrap w-full">
                                <svg class="inline-block w-5 h-5 mr-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Undang
                            </button>
                        </div>
                    </form>

                    <?php if ($isPic && !empty($pendingInvitations)): ?>
                        <!-- Section 1: Invitations sent by PIC (waiting for user response) -->
                        <hr class="h-px py-4 text-gray-400 mt-6">
                        <p class="font-bold text-2xl mb-4">
                            Menunggu Konfirmasi
                        </p>
                        <p class="text-sm text-slate-500 mb-4">Undangan yang Anda kirim, menunggu respon dari user</p>
                        <div class="space-y-3 mb-6">
                            <?php foreach ($pendingInvitations as $invitation): ?>
                                <div class="flex items-center justify-between p-4 bg-amber-50 rounded-xl border border-amber-200">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800"><?= htmlspecialchars($invitation['nama']) ?></p>
                                            <p class="text-sm text-slate-500"><?= htmlspecialchars($invitation['email']) ?></p>
                                        </div>
                                    </div>
                                    <form action="/invitations/cancel" method="post">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="invitation_id" value="<?= (int) $invitation['id_invitation'] ?>">
                                        <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
                                        <button type="submit"
                                            class="px-3 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                                            Batalkan
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($isPic && !empty($joinRequests)): ?>
                        <!-- Section 2: Join requests (users who used invite token) -->
                        <hr class="h-px py-4 text-gray-400 mt-6">
                        <p class="font-bold text-2xl mb-4">
                            Permintaan Bergabung
                        </p>
                        <p class="text-sm text-slate-500 mb-4">User yang ingin bergabung via kode undangan</p>
                        <div class="space-y-3 mb-6">
                            <?php foreach ($joinRequests as $request): ?>
                                <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-200">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800"><?= htmlspecialchars($request['nama']) ?></p>
                                            <p class="text-sm text-slate-500"><?= htmlspecialchars($request['email']) ?></p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <form action="/invitations/approve" method="post">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="invitation_id"
                                                value="<?= (int) $request['id_invitation'] ?>">
                                            <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
                                            <button type="submit"
                                                class="px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-colors flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                Terima
                                            </button>
                                        </form>
                                        <form action="/invitations/reject-request" method="post">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="invitation_id"
                                                value="<?= (int) $request['id_invitation'] ?>">
                                            <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
                                            <button type="submit"
                                                class="px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-lg hover:bg-red-200 transition-colors flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Requirement Warning -->
                    <?php if (!$canSubmit): ?>
                        <div class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm font-semibold text-yellow-800">Anggota Belum Mencukupi</p>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        Minimal <?= (int) $booking->required_members ?> anggota diperlukan. Saat ini:
                                        <?= (int) $booking->current_members ?>
                                        anggota.
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if ($isPic): ?>
        <div class="w-full mb-16">
            <form action="/bookings/delete-draft" method="post"
                onsubmit="return confirm('Yakin ingin menghapus draft ini?');">
                <?= csrf_field() ?>
                <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
                <button type="submit"
                    class="inline-block bg-red-600 hover:bg-red-700 font-regular text-sm text-white w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all capitalize">
                    Hapus draft booking
                </button>
            </form>
        </div>
    <?php endif; ?>
    <?php if ($isPic): ?>
        <div class="bg-white rounded-2xl shadow-lg p-4 mb-12 hidden md:block">
            <form action="/bookings/submit" method="post"
                onsubmit="return confirm('Anda yakin ingin mengirimkan pengajuan ini?')">
                <?= csrf_field() ?>
                <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">

                <button type="submit" <?= ($booking->status !== 'draft' || !$canSubmit) ? 'disabled' : '' ?>
                    class="w-full bg-primary text-green-200 px-8 py-4 rounded-xl hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl disabled:bg-gray-200 disabled:cursor-not-allowed disabled:hover:shadow-lg flex items-center justify-center disabled:text-black">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <?= $canSubmit ? 'Kirim ke Admin' : 'Lengkapi Anggota Terlebih Dahulu' ?>
                </button>
            </form>
        </div>
    <?php endif; ?>

    <nav class="fixed left-0 bottom-0 right-0 bg-gray-100 text-white md:hidden z-999 rounded-t-4xl py-6 shadow-xl">
        <div class="flex items-center justify-around w-full px-4">
            <form action="/bookings/submit" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">

                <button type="submit" <?= ($booking->status !== 'draft' || !$canSubmit) ? 'disabled' : '' ?>
                    class="w-full bg-primary text-green-200 px-8 py-4 rounded-xl border hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl disabled:border-slate-400 disabled:bg-slate-200 disabled:cursor-not-allowed disabled:hover:shadow-lg flex items-center justify-center disabled:text-black">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <?= $canSubmit ? 'Kirim ke Admin' : 'Lengkapi Anggota Terlebih Dahulu' ?>
                </button>
            </form>

        </div>
    </nav>

    <?php if ($isPic && $booking->status !== 'draft'): ?>
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <!-- Cancel Booking (PIC only) -->
            <form action="/bookings/cancel" method="post"
                onsubmit="return confirm('Yakin ingin membatalkan booking ini?');">
                <?= csrf_field() ?>
                <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
                <button type="submit"
                    class="w-full bg-red-500 text-white px-8 py-4 rounded-xl hover:bg-red-600 transition-all font-semibold shadow-lg flex items-center justify-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Batalkan Booking
                </button>
            </form>
        <?php elseif ($isMember): ?>
            <!-- Leave Booking (Member only) -->
            <form action="/bookings/leave" method="post" onsubmit="return confirm('Yakin ingin keluar dari booking ini?');">
                <?= csrf_field() ?>
                <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">
                <button type="submit"
                    class="w-full bg-gray-500 text-white px-8 py-4 rounded-xl hover:bg-gray-600 transition-all font-semibold shadow-lg flex items-center justify-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Keluar dari Booking
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

</div>
