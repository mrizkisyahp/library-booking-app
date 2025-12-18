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
    <div class="pb-12 md:mb-0 p-6 md:bg-primary md:rounded-2xl
            grid place-items-center h-fit md:min-h-[60dvh] justify-center">
        <div class="text-center text-white space-y-4">
            <div class="p-6 w-fit rounded-3xl bg-emerald-800 border-2 border-emerald-700 mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
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
        <div class="bg-white rounded-t-3xl md:rounded-3xl shadow-lg p-6 border border-gray-100 mb-12 md:mb-0 md:h-[60vh]">
            <div class="mb-0">
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
                <!-- logout module -->
                <?php if (auth()->guest()): ?>
                    <!-- Empty -->
                <?php else: ?>
                    <a href="/profile#modal-logout-mobile"
                        class="flex md:hidden items-center w-full text-lg gap-4 justify-center px-8 py-4 rounded-lg hover:bg-rose-700 bg-rose-600 md:bg-primary shadow-md text-white transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:bg-rose-600 focus:ring-rose-500 focus:ring-offset-2">
                        <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </a>
                    <!-- modal menu logout-->
                    <div id="modal-logout-mobile"
                        class="fixed inset-0 bg-black/50 opacity-0 pointer-events-none duration-300 transition-all target:opacity-100 target:pointer-events-auto flex justify-center items-center z-999 backdrop-blur-xs">

                        <div
                            class="bg-white p-6 rounded-2xl w-11/12 max-w-md shadow-lg scale-95 transition-all duration-300 target:scale-100 relative">

                            <h1 class="text-4xl font-bold text-slate-800 mb-2">
                                Peringatan
                            </h1>
                            <p class="text-sm text-slate-600 mb-4">
                                Apakah Kamu yakin ingin keluar dari aplikasi?
                            </p>

                            <form action="/logout" method="post" class="space-y-3">
                                <?= csrf_field() ?>

                                <div class="flex items-center gap-4 mt-6 text-center">
                                    <a href="/profile"
                                        class="w-full bg-slate-200 text-black p-4 rounded-2xl hover:bg-slate-300 transition-all font-regular border border-slate-400 shadow cursor-pointer">
                                        Tidak
                                    </a>

                                    <button type="submit"
                                        class="w-full bg-rose-500 text-white p-4 rounded-2xl hover:bg-rose-600 transition-all font-regular border border-rose-700 shadow cursor-pointer">
                                        Ya, Keluar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </div>
<?php endif; ?>