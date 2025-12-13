<div class="p-6 bg-white shadow-md w-full hidden md:block">
    <div class="flex items-center">
        <div class="">
            <span class="text-black font-bold text-4xl font-plus-jakarta-sans">
                Akun
            </span>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto md:grid md:grid-cols-2 gap-4 p-6 h-dvh md:h-[80dvh]">
<!-- Header -->
<div class="mb-12 md:mb-0 p-6 md:bg-primary md:rounded-2xl
            grid place-items-center h-fit md:min-h-[60vh] justify-center">
    <div class="text-center text-white space-y-4">
        <div class="p-6 w-fit rounded-3xl bg-emerald-800 border-2 border-emerald-700 mx-auto">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="size-16">
                <circle cx="12" cy="8" r="5" />
                <path d="M20 21a8 8 0 0 0-16 0" />
            </svg>
        </div>

        <p class="font-medium text-2xl capitalize">
            <?= htmlspecialchars($user->nama) ?>
        </p>

        <p class="text-base opacity-90">
            <?= htmlspecialchars($user->email) ?>
        </p>

        <div class="inline-flex items-center gap-3 px-4 py-2 rounded-2xl border
            <?= $user->status === 'active'
                ? 'bg-green-100 text-green-800 border-green-500'
                : 'bg-red-100 text-red-800 border-red-500' ?>">
            <span>Status:</span>
            <span class="font-semibold"><?= ucfirst($user->status) ?></span>
        </div>
    </div>

</div>


    <?php if ($user): ?>
        <!-- Profile Information Card -->
        <div class="bg-white rounded-t-3xl md:rounded-3xl shadow-lg p-6 border border-gray-100 mb-6 md:mb-0 md:h-[60vh]">
            <div class="mb-8">
                <a href="/profile/verifikasi" class="border-b border-gray-200">
                    <div class="flex items-center justify-between">

                        <div class="flex items-center gap-4 py-4 px-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-badge-check-icon lucide-badge-check size-6">
                                <path
                                    d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" />
                                <path d="m9 12 2 2 4-4" />
                            </svg>
                            <span class="capitalize">
                                verifikasi akun
                            </span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-chevron-right-icon lucide-chevron-right">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </div>
                </a>
                <a href="/profile/detail" class="border-b border-gray-200">
                    <div class="flex items-center justify-between">

                        <div class="flex items-center gap-4 py-4 px-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-user-round-search-icon lucide-user-round-search size-6">
                                <circle cx="10" cy="8" r="5" />
                                <path d="M2 21a8 8 0 0 1 10.434-7.62" />
                                <circle cx="18" cy="18" r="3" />
                                <path d="m22 22-1.9-1.9" />
                            </svg> <span class="capitalize">
                                detail akun
                            </span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-chevron-right-icon lucide-chevron-right">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </div>
                </a>
                <a href="/profile/reset-password" class="border-b border-gray-200">
                    <div class="flex items-center justify-between">

                        <div class="flex items-center gap-4 py-4 px-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-key-square-icon lucide-key-square size-6">
                                <path
                                    d="M12.4 2.7a2.5 2.5 0 0 1 3.4 0l5.5 5.5a2.5 2.5 0 0 1 0 3.4l-3.7 3.7a2.5 2.5 0 0 1-3.4 0L8.7 9.8a2.5 2.5 0 0 1 0-3.4z" />
                                <path d="m14 7 3 3" />
                                <path
                                    d="m9.4 10.6-6.814 6.814A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814" />
                            </svg>
                            <span class="capitalize">
                                ubah kata sandi
                            </span>
                        </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-chevron-right-icon lucide-chevron-right">
                                <path d="m9 18 6-6-6-6" />
                            </svg>
                    </div>
                </a>
                <a href="/profile/faq" class="border-b border-gray-200">
                    <div class="flex items-center justify-between">

                        <div class="flex items-center gap-4 py-4 px-6">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-scale-icon lucide-scale size-6">
                                <path d="M12 3v18" />
                                <path d="m19 8 3 8a5 5 0 0 1-6 0zV7" />
                                <path d="M3 7h1a17 17 0 0 0 8-2 17 17 0 0 0 8 2h1" />
                                <path d="m5 8 3 8a5 5 0 0 1-6 0zV7" />
                                <path d="M7 21h10" />
                            </svg>
                            <span class="capitalize">
                                aturan dan panduan aplikasi
                            </span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-chevron-right-icon lucide-chevron-right">
                            <path d="m9 18 6-6-6-6" />
                        </svg>
                    </div>
                </a>
            </div>
            <div class="mb-8">
                <?php if (auth()->user()->isMahasiswa()): ?>
                    <?php if ($user->status === 'rejected'): ?>
                        <!-- Rejected state - Show upload form again -->
                        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                            <div class="flex items-start mb-6">
                                <svg class="w-6 h-6 text-red-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Bukti KuBaca Ditolak</h3>
                                    <p class="text-gray-600">Gambar KuBaca-mu ditolak. Mohon unggah foto baru yang lebih jelas.</p>
                                </div>
                            </div>
                            <form action="/upload-kubaca" method="post" enctype="multipart/form-data" class="space-y-6">
                                <?= csrf_field() ?>
                                <div>
                                    <label for="kubaca_img" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Gambar KuBaca Baru
                                    </label>
                                    <input type="file" id="kubaca_img" name="kubaca_img" accept="image/png,image/jpeg,image/webp" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                                    <p class="mt-2 text-sm text-gray-500">Format: PNG, JPEG, atau WebP (Maks 5MB)</p>
                                </div>
                                <button type="submit"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        Unggah Ulang Foto KuBaca
                                    </span>
                                </button>
                            </form>
                        </div>
                    <?php elseif ($user->status === 'pending kubaca' && !$user->kubaca_img): ?>
                        <!-- Initial KuBaca Upload Card -->
                        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                            <div class="flex items-start mb-6">
                                <svg class="w-6 h-6 text-blue-600 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Unggah KuBaca PNJ</h3>
                                    <p class="text-gray-600">Silakan unggah foto KuBaca Anda untuk menyelesaikan verifikasi</p>
                                </div>
                            </div>
                            <form action="/upload-kubaca" method="post" enctype="multipart/form-data" class="space-y-6">
                                <?= csrf_field() ?>
                                <div>
                                    <label for="kubaca_img" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Gambar KuBaca
                                    </label>
                                    <input type="file" id="kubaca_img" name="kubaca_img" accept="image/png,image/jpeg,image/webp" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                                    <p class="mt-2 text-sm text-gray-500">Format: PNG, JPEG, atau WebP (Maks 5MB)</p>
                                </div>
                                <button type="submit"
                                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                                    <span class="flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        Unggah KuBaca
                                    </span>
                                </button>
                            </form>
                        </div>
                    <?php elseif ($user->kubaca_img && $user->status === 'pending kubaca'): ?>
                        <!-- Waiting for admin verification -->
                        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-lg mb-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-orange-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd" />
                                </svg>
                                <p class="text-orange-800 font-medium">Gambar KuBaca telah diunggah. Tunggu admin untuk verifikasi.
                                </p>
                            </div>
                        </div>
                    <?php elseif ($user->status === 'active'): ?>
                        <!-- Active/Verified state -->
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <p class="text-green-800 font-medium">Akunmu sudah terverifikasi!</p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php elseif ((auth()->user()->isDosen() || auth()->user()->isTendik()) && $user->status === 'active'): ?>
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p class="text-green-800 font-medium">Akunmu sudah terverifikasi!</p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- logout module -->
                <?php if (auth()->guest()): ?>
                    <!-- Empty -->
                <?php else: ?>
                    <form action="/logout" method="post">
                        <?= csrf_field() ?>
                        <button type="submit"
                            class="flex md:hidden items-center w-full text-lg gap-4 justify-center px-8 py-4 rounded-lg hover:bg-rose-700 bg-rose-600 md:bg-primary shadow-md text-white transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:bg-rose-600 focus:ring-rose-500 focus:ring-offset-2">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </div>
<?php endif; ?>
