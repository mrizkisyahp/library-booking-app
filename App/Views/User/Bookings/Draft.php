<?php

use App\Core\App;
use App\Models\Booking;
use App\Core\Csrf;
use App\Models\User;

$currentUser = App::$app->user instanceof User ? App::$app->user : null;
$isOwner = $currentUser && (int) $currentUser->id_user === (int) $booking->user_id;

/** @var Booking $booking */
?>

<!-- Welcome Header -->
<div class="p-4 bg-white shadow-md w-full ">
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
                <?= htmlspecialchars($room->nama_ruangan) ?>
            </p>
            <p class="mb-2">
                <?= htmlspecialchars($room->jenis_ruangan) ?>
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
                    <?= (int) $currentMembers ?> /
                    <?= isset($maximumMembers) && $maximumMembers > 0 ? (int) $maximumMembers : '∞' ?> peserta
                    <?php if (isset($requiredMembers) && $requiredMembers > 0): ?>
                        · Min <?= (int) $requiredMembers ?>
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
                    <?php
                    // htmlspecialchars($statusLabel)
                    ?>
                    <!-- 🤡🤡🤡 -->
                    <!-- if $booking['status'] == 'draft' && $requiredMembers == 0:
                      echo ('(Menunggu Anggota)');
                      elseif $booking['status'] == 'draft' && $requiredMembers > 0:
                        echo ('(Siap Dikirim)');
                      elseif $booking['status'] == 'pending':
                        echo ('(Menunggu Konfirmasi)');
                      elseif $booking['status'] == 'verified':
                        echo ('(Terkonfirmasi)');
                      elseif $booking['status'] == 'active':
                        echo ('(Sedang berlangsung)');
                      elseif $booking['status'] == 'completed':
                        echo ('(Selesai)');
                    endif; -->
                    <?php if ($booking->status === 'draft'): ?>
                        (Menunggu Anggota)
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

                <div class="w-full mb-4">
                    <a href="/bookings/draft?id=<?= (int) $booking->id_booking ?>"
                        class="inline-block bg-primary hover:bg-emerald-700 font-regular text-sm text-white w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all capitalize">
                        ubah rincian booking
                    </a>
                    <?php if ($booking->role === 'PIC' && $statusKey === 'completed' && empty($booking['feedback_submitted'])): ?>
                        <a href="/feedback/create?booking=<?= (int) $booking->id_booking ?>"
                            class="inline-block text-emerald-600 hover:text-emerald-700 font-regular text-sm active:text-emerald-800 w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide underline focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all">
                            Isi Feedback
                        </a>
                    <?php endif; ?>
                </div>

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

                <hr class="h-px py-4 text-gray-400">
                <?php if ($isOwner): ?>
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
                            <?= (int) $currentMembers ?> /
                            <?= isset($maximumMembers) && $maximumMembers > 0 ? (int) $maximumMembers : '∞' ?> peserta
                            <?php if (isset($requiredMembers) && $requiredMembers > 0): ?>
                                · Min <?= (int) $requiredMembers ?>
                            <?php endif; ?>
                            <?= isset($maximumMembers) && $maximumMembers > 0 ? (int) $maximumMembers : '∞' ?> Orang
                        </span>
                    </p>
                <?php endif; ?>

                <?php if (isset($maximumMembers) && $maximumMembers > 0 && $currentMembers >= $maximumMembers): ?>
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

                <?php $members = $booking->getMembers(); ?>
                <?php if (empty($members)): ?>
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
                        <?php foreach ($members as $member): ?>
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
                                        </span>
                                    </div>

                                    <?php if (!empty($member['is_owner'])): ?>
                                        <span
                                            class="text-xs text-white font-semibold flex items-center mt-1 bg-emerald-600 p-2 rounded-md border border-emerald-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="lucide lucide-crown-icon lucide-crown">
                                                <path
                                                    d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z" />
                                                <path d="M5 21h14" />
                                            </svg>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($isOwner): ?>
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
                                        Minimal <?= (int) $requiredMembers ?> anggota diperlukan. Saat ini:
                                        <?= (int) $currentMembers ?>
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
    <div class="w-full mb-8">
        <a href="/bookings/draft?id=<?= (int) $booking->id_booking ?>"
            class="inline-block bg-red-600 hover:bg-red-700 font-regular text-sm text-white w-full px-4 py-2 rounded-xl text-center mb-4 font-regular tracking-wide focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all capitalize">
            Hapus draft booking
        </a>
    </div>
    <?php if ($isOwner): ?>
        <div class="bg-white rounded-2xl shadow-lg p-4 mb-12 hidden md:block">
            <form action="/bookings/submit" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="booking_id" value="<?= (int) $booking->id_booking ?>">

                <button type="submit" <?= ($booking->status !== 'draft' || !$canSubmit) ? 'disabled' : '' ?>
                    class="w-full bg-primary text-gray-500 px-8 py-4 rounded-xl hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl disabled:bg-gray-200 disabled:cursor-not-allowed disabled:hover:shadow-lg flex items-center justify-center">
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
                    class="w-full bg-primary text-slate-500 px-8 py-4 rounded-xl border hover:bg-emerald-700 transition-all font-semibold shadow-lg hover:shadow-xl disabled:border-slate-400 disabled:bg-slate-200 disabled:cursor-not-allowed disabled:hover:shadow-lg flex items-center justify-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <?= $canSubmit ? 'Kirim ke Admin' : 'Lengkapi Anggota Terlebih Dahulu' ?>
                </button>
            </form>

        </div>
    </nav>

</div>
