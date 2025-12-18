<?php
use App\Core\App;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(App::$app->response->resolveTitle(App::$app->controller)) ?></title>
    <base href="<?= App::getBaseUrl() ?>/" />
    <link href="css/output.css" rel="stylesheet">
</head>

<body class="min-h-dvh bg-slate-100">
    <!-- sidebar -->
    <aside
        class="hidden md:flex group flex-col fixed items-start justify-between overflow-hidden left-0 top-0 h-dvh bg-primary text-white rounded-r-3xl transition-all duration-300 w-24 hover:w-80 z-10 border-r border-emerald-700">
        <ul class="flex flex-col mt-10 space-y-4 w-full px-3">
            <!-- sidebar items -->
            <!-- Logo -->
             <li class="w-full px-3">
                <div
                    class="flex items-center gap-4 p-3 w-full  ">
                    <img src="/src/logoPinrupus.png" alt="Logo" class="size-6 shrink-0">
                    <span
                        class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0 font-semibold text-lg tracking-wide">
                        PinRuPus PNJ
                    </span>
            </div>
             </li>
            <li class="w-full px-3">
                <a class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all "
                    <?php if (auth()->guest()): ?>
                        href="/"
                    <?php else: ?>
                        <?php if (auth()->check() && auth()->user()->isAdmin()): ?>
                            href="/admin"
                        <?php else: ?>
                            href="/dashboard"
                        <?php endif; ?>
                    <?php endif; ?>>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-house size-6 shrink-0">
                        <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" />
                        <path
                            d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    </svg>
                    <span
                        class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                        Library Booking App
                    </span>
                </a>
            </li>
            <!-- Main navigation 😋-->
            <?php if (auth()->guest()): ?>
                <!-- Guest 🥸 -->
                <li class="w-full px-3">
                    <a href="/login"
                        class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-circle-icon lucide-circle size-6 shrink-0">
                            <circle cx="12" cy="12" r="10" />
                        </svg>
                        <span
                            class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                            Login
                        </span>
                    </a>
                </li>
                <li class="w-full px-3">
                    <a href="/register"
                        class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-circle-icon lucide-circle size-6 shrink-0">
                            <circle cx="12" cy="12" r="10" />
                        </svg>
                        <span
                            class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                            Register
                        </span>
                    </a>
                </li>
            <?php else: ?>
                <!-- User Admin 🧑🏻‍💻 -->
                <?php if (auth()->check() && auth()->user()->isAdmin()): ?>
                    <li class="w-full px-3">
                        <a href="/admin/bookings"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-book-open-text-icon lucide-book-open-text size-6 shrink-0">
                                <path d="M12 7v14" />
                                <path d="M16 12h2" />
                                <path d="M16 8h2" />
                                <path
                                    d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z" />
                                <path d="M6 12h2" />
                                <path d="M6 8h2" />
                            </svg>
                            <span
                                class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                                Booking
                            </span>
                        </a>
                    </li>
                    <li class="w-full px-3">
                        <a href="/admin/rooms"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-door-open-icon lucide-door-open size-6 shrink-0">
                                <path d="M11 20H2" />
                                <path
                                    d="M11 4.562v16.157a1 1 0 0 0 1.242.97L19 20V5.562a2 2 0 0 0-1.515-1.94l-4-1A2 2 0 0 0 11 4.561z" />
                                <path d="M11 4H8a2 2 0 0 0-2 2v14" />
                                <path d="M14 12h.01" />
                                <path d="M22 20h-3" />
                            </svg>
                            <span
                                class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                                Ruangan
                            </span>
                        </a>
                    </li>
                    <li class="w-full px-3">
                        <a href="/admin/users"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-users-round-icon lucide-users-round size-6 shrink-0">
                                <path d="M18 21a8 8 0 0 0-16 0" />
                                <circle cx="10" cy="8" r="5" />
                                <path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3" />
                            </svg>
                            <span
                                class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                                User
                            </span>
                        </a>
                    </li>
                    <li class="w-full px-3">
                        <a href="/admin/reports"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-file-text-icon lucide-file-text size-6 shrink-0">
                                <path
                                    d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z" />
                                <path d="M14 2v5a1 1 0 0 0 1 1h5" />
                                <path d="M10 9H8" />
                                <path d="M16 13H8" />
                                <path d="M16 17H8" />
                            </svg>
                            <span
                                class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                                Laporan
                            </span>
                        </a>
                    </li>
                    <li class="w-full px-3">
                        <a href="/rooms"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-calendar-plus-icon lucide-calendar-plus size-6 shrink-0">
                                <path d="M16 19h6" />
                                <path d="M16 2v4" />
                                <path d="M19 16v6" />
                                <path d="M21 12.598V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8.5" />
                                <path d="M3 10h18" />
                                <path d="M8 2v4" />
                            </svg> <span
                                class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                                Buat Booking
                            </span>
                        </a>
                    </li>
                    <li class="w-full px-3">
                        <a href="/admin/settings"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-settings size-6 shrink-0">
                                <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                                Pengaturan
                            </span>
                        </a>
                    </li>
                    <!-- User Biasa 😄 -->
                <?php else: ?>
                    <li class="w-full px-3">
                        <a href="/rooms"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-search-icon lucide-search size-6 shrink-0">
                                <path d="m21 21-4.34-4.34" />
                                <circle cx="11" cy="11" r="8" />
                            </svg>
                            <span
                                class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                                Cari
                            </span>
                        </a>
                    </li>
                    <li class="w-full px-3">
                        <a href="/my-bookings"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-book-open-text-icon lucide-book-open-text size-6 shrink-0">
                                <path d="M12 7v14" />
                                <path d="M16 12h2" />
                                <path d="M16 8h2" />
                                <path
                                    d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z" />
                                <path d="M6 12h2" />
                                <path d="M6 8h2" />
                            </svg>
                            <span
                                class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                                Booking
                            </span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
        <!-- Bagian bawah 🤔 -->
        <ul class="flex flex-col mt-10 space-y-4 w-full px-3">
            <?php if (auth()->guest()): ?>
                <!-- Empty -->
            <?php else: ?>
                <li class="mx-2 px-2">
                    <a href="/profile"
                        class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-user-round-icon lucide-user-round size-6 shrink-0">
                            <circle cx="12" cy="8" r="5" />
                            <path d="M20 21a8 8 0 0 0-16 0" />
                        </svg>
                        <span
                            class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                            Profil
                        </span>
                    </a>
                </li>
                <li class="mx-2 px-2">
                    <!-- button logout -->
                        <a href="#modal-logout"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-red-600 transition-all mb-4 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-log-out-icon lucide-log-out size-6 shrink-0">
                                <path d="m16 17 5-5-5-5" />
                                <path d="M21 12H9" />
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            </svg> <span
                                class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                                Logout
                            </span>
                        </a>

                <!-- modal menu logout-->
                <div id="modal-logout"
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
                                <a href="#"
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

                </li>
            <?php endif; ?>
        </ul>
    </aside>
    <!-- Main Content -->
    <main class="py-12 md:pt-0 min-h-dvh md:pl-24">
        <div class="mx-auto bg-primary md:bg-slate-100">

            <div class="bg-white top-0 left-0 w-24 h-40 fixed -z-10 hidden md:block">
                <!-- empty div for bg -->
            </div>
            <!-- Flash Toaster Messages -->
            <?php if ($m = App::$app->session->getFlash('success')): ?>
                <input type="checkbox" id="toast-success" class="peer hidden" checked>

                <div
                    class="fixed top-6 left-1/2 -translate-x-1/2 z-50 w-[90%] max-w-lg peer-checked:flex hidden bg-green-50 border border-emerald-500 text-emerald-800 rounded-2xl shadow-xl p-4 transition-all">
                    <div class="flex items-start gap-3 w-full">
                        <svg class="w-6 h-6 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>

                        <p class="flex-1 font-medium">
                            <?= nl2br(htmlspecialchars($m)) ?>
                        </p>

                        <label for="toast-success"
                            class="cursor-pointer text-emerald-600 hover:text-emerald-800 text-xl leading-none">
                            &times;
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($m = App::$app->session->getFlash('error')): ?>
                <input type="checkbox" id="toast-error" class="peer hidden" checked>

                <div
                    class="fixed top-6 left-1/2 -translate-x-1/2 z-50 w-[90%] max-w-lg peer-checked:flex hidden bg-red-50 border border-red-500 text-red-800 rounded-2xl shadow-xl p-4 transition-all">
                    <div class="flex items-start gap-3 w-full">
                        <svg class="w-6 h-6 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>

                        <p class="flex-1 font-medium">
                            <?= nl2br(htmlspecialchars($m)) ?>
                        </p>

                        <label for="toast-error"
                            class="cursor-pointer text-red-600 hover:text-red-800 text-xl leading-none">
                            &times;
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($m = App::$app->session->getFlash('warning')): ?>
                <input type="checkbox" id="toast-warning" class="peer hidden" checked>

                <div
                    class="fixed top-6 left-1/2 -translate-x-1/2 z-50 w-[90%] max-w-lg peer-checked:flex hidden bg-yellow-50 border border-yellow-500 text-yellow-800 rounded-2xl shadow-xl p-4 transition-all">
                    <div class="flex items-start gap-3 w-full">
                        <svg class="w-6 h-6 text-yellow-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>

                        <p class="flex-1 font-medium">
                            <?= nl2br(htmlspecialchars($m)) ?>
                        </p>

                        <label for="toast-warning"
                            class="cursor-pointer text-yellow-600 hover:text-yellow-800 text-xl leading-none">
                            &times;
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($m = App::$app->session->getFlash('info')): ?>
                <input type="checkbox" id="toast-info" class="peer hidden" checked>

                <div
                    class="fixed top-6 left-1/2 -translate-x-1/2 z-50 w-[90%] max-w-lg peer-checked:flex hidden bg-blue-50 border border-blue-500 text-blue-800 rounded-2xl shadow-xl p-4 transition-all">
                    <div class="flex items-start gap-3 w-full">
                        <svg class="w-6 h-6 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>

                        <p class="flex-1 font-medium">
                            <?= nl2br(htmlspecialchars($m)) ?>
                        </p>

                        <label for="toast-info"
                            class="cursor-pointer text-blue-600 hover:text-blue-800 text-xl leading-none">
                            &times;
                        </label>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            {{content}}
        </div>
    </main>
    <!-- Footer -->
    <footer class="bg-primary text-white py-8 mt-auto hidden md:block">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-center md:text-left">
                    <p class="font-semibold text-lg">Library Booking App PNJ</p>
                    <p class="text-emerald-100 text-sm mt-1">Sistem Peminjaman Ruangan Perpustakaan</p>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-emerald-100 text-sm">&copy; <?= date('Y') ?> All rights reserved</p>
                    <p class="text-emerald-100 text-sm mt-1">Politeknik Negeri Jakarta</p>
                </div>
            </div>
        </div>
    </footer>
    <!-- mobile guest checker -->
    <?php if (auth()->guest()): ?>
        <div class="min-h-dvh bg-black/30 backdrop-blur-md z-50">
            <div class="bg-white shadow-lg fixed flex justify-center items-center">
                <a href="/login">Login</a>
                <a href="/register">Register</a>
            </div>
        </div>
    <?php endif; ?>
    <!-- mobile Navigation -->
    <header
        class="fixed left-0 top-0 right-0 bg-primary text-white w-full flex items-center justify-between px-6 py-4 z-40 md:hidden">

        <div class="flex items-center gap-2"><img src="/src/logoPinrupus.png" alt="" class="size-8 "> PINRUPUS PNJ</div>
        <div>
            <a href="#">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-bell-icon lucide-bell size-6">
                    <path d="M10.268 21a2 2 0 0 0 3.464 0" />
                    <path
                        d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326" />
                </svg>
            </a>
        </div>
    </header>
    <nav class="fixed left-0 -bottom-1 right-0 bg-primary text-white md:hidden z-50 rounded-t-4xl py-3 shadow-xl">
        <div class="flex items-center justify-around w-full px-4">
            <?php
            $active = $_SERVER['REQUEST_URI'];
            $currentPageQuery = basename($active);
            $currentPage = explode('?', $currentPageQuery)[0];
            if (auth()->guest()) {
                $url = '/';
            } else {
                $url = auth()->check() && auth()->user()->isAdmin() ? '/admin' : '/dashboard';
            }
            function isActiveClass($current, $target)
            {
                return $current === $target
                    ? "bg-emerald-800/60 border-2 border-white/30 rounded-2xl px-4 py-2"
                    : "opacity-70 hover:opacity-100 transition px-4 py-2";
            }
            ?>
            <!-- beranda -->
            <a href="<?= $url ?>"
                class="flex flex-col size-16 gap-1.5 text-sm items-center  <?= isActiveClass($active, $url) ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-home size-6">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                <span>Beranda</span>
            </a>
            <?php if (auth()->check() && auth()->user()->isAdmin()): ?>
                <!-- manajemen all -->
                <a href="/admin/bookings"
                    class="flex flex-col gap-1.5 text-sm items-center <?= isActiveClass($active, "/admin/bookings") ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-square-chart-gantt size-6">
                        <rect width="18" height="18" x="3" y="3" rx="2" />
                        <path d="M9 8h7" />
                        <path d="M8 12h6" />
                        <path d="M11 16h5" />
                    </svg>
                    <span>Manajemen</span>
                </a>
                <!-- laporan/report -->
                <a href="/reports"
                    class="flex flex-col gap-1.5 text-sm items-center <?= isActiveClass($active, "/reports") ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" stroke="currentColor" stroke-width="2"
                        fill="none" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-door-open size-6">
                        <path d="M11 20H2" />
                        <path
                            d="M11 4.562v16.157a1 1 0 0 0 1.242.97L19 20V5.562a2 2 0 0 0-1.515-1.94l-4-1A2 2 0 0 0 11 4.561z" />
                        <path d="M11 4H8a2 2 0 0 0-2 2v14" />
                        <path d="M14 12h.01" />
                        <path d="M22 20h-3" />
                    </svg>
                    <span>Laporan</span>
                </a>
            <?php else: ?>
                <!-- Cari ruangan -->
                <a href="/rooms" class="flex flex-col gap-1.5 text-sm items-center <?= isActiveClass($active, "/rooms") ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-search-icon lucide-search size-6">
                        <path d="m21 21-4.34-4.34" />
                        <circle cx="11" cy="11" r="8" />
                    </svg>
                    <span>Cari</span>
                </a>
                <!-- buking -->
                <a href="/my-bookings"
                    class="flex flex-col gap-1.5 text-sm items-center <?= isActiveClass($active, "/my-bookings") ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-book-open-text-icon lucide-book-open-text size-6">
                        <path d="M12 7v14" />
                        <path d="M16 12h2" />
                        <path d="M16 8h2" />
                        <path
                            d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z" />
                        <path d="M6 12h2" />
                        <path d="M6 8h2" />
                    </svg>
                    <span>Booking</span>
                </a>
            <?php endif; ?>
            <!-- profil -->
            <a href="/profile"
                class="flex flex-col gap-1.5 text-sm items-center <?= isActiveClass($active, "/profile") ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-user-round-icon lucide-user-round size-6">
                    <circle cx="12" cy="8" r="5" />
                    <path d="M20 21a8 8 0 0 0-16 0" />
                </svg>
                <span>Profil</span>
            </a>
        </div>
    </nav>
</body>

</html>
<script src="src/script.js"></script>
