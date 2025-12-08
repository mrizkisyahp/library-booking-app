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
<<<<<<< HEAD

<body class="min-h-dvh bg-slate-100">
    <!-- sidebar -->
=======
<body class="min-h-screen bg-slate-100">
    <!-- sidebar 😭 -->
>>>>>>> revampwf
    <aside class="hidden md:flex group flex-col fixed items-start justify-between overflow-hidden left-0 top-0 h-dvh bg-primary text-white rounded-r-3xl transition-all duration-300 w-28 hover:w-80 z-10">
        <ul class="flex flex-col mt-10 space-y-4 w-full px-3">
            <!-- sidebar items -->
            <!-- Logo -->
            <li class="w-full px-3">
                <a class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all"
<<<<<<< HEAD
                    <?php if (App::$app->auth->isGuest()): ?>
                        href="/"
                    <?php else: ?>
                        <?php if ($user && $user->isAdmin()): ?>
                            href="/admin"
=======
                    <?php if (auth()->guest()): ?> 
                        href="/" 
                    <?php else: ?> 
                        <?php if (auth()->check() && auth()->user()->isAdmin()): ?>
                            href="/admin" 
>>>>>>> revampwf
                        <?php else: ?>
                            href="/dashboard"
                        <?php endif; ?>
                    <?php endif; ?>
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-house size-6 shrink-0">
                        <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" />
                        <path
                            d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    </svg>
                    <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                        Library Booking App
                    </span>
                </a>
            </li>
<<<<<<< HEAD

            <!-- Main navigation -->
            <?php if (App::$app->auth->isGuest()): ?>
                <!-- Guest -->
=======
            <!-- Main navigation 😋-->
            <?php if (auth()->guest()): ?>
                <!-- Guest 🥸 -->
>>>>>>> revampwf
                <li class="w-full px-3">
                    <a href="/login"
                        class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-icon lucide-circle size-6 shrink-0"><circle cx="12" cy="12" r="10"/></svg>
                        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                            Login
                        </span>
                    </a>
                </li>
                <li class="w-full px-3">
                    <a href="/register"
                        class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-icon lucide-circle size-6 shrink-0"><circle cx="12" cy="12" r="10"/></svg>
                        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                            Register
                        </span>
                    </a>
                </li>
            <?php else: ?>
<<<<<<< HEAD
                <!-- User Admin -->
                <?php if ($user && $user->isAdmin()): ?>
=======
                <!-- User Admin 🧑🏻‍💻 -->
                <?php if (auth()->check() && auth()->user()->isAdmin()): ?>
>>>>>>> revampwf
                    <li class="w-full px-3">
                        <a href="/admin/bookings"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-open-text-icon lucide-book-open-text size-6 shrink-0"><path d="M12 7v14"/><path d="M16 12h2"/><path d="M16 8h2"/><path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z"/><path d="M6 12h2"/><path d="M6 8h2"/></svg>
                            <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                                Booking
                            </span>
                        </a>
                    </li>
                    <li class="w-full px-3">
                        <a href="/admin/rooms"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-door-open-icon lucide-door-open size-6 shrink-0"><path d="M11 20H2"/><path d="M11 4.562v16.157a1 1 0 0 0 1.242.97L19 20V5.562a2 2 0 0 0-1.515-1.94l-4-1A2 2 0 0 0 11 4.561z"/><path d="M11 4H8a2 2 0 0 0-2 2v14"/><path d="M14 12h.01"/><path d="M22 20h-3"/></svg>
                            <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                                Ruangan
                            </span>
                        </a>
                    </li>
                    <li class="w-full px-3">
                        <a href="/admin/users"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users-round-icon lucide-users-round size-6 shrink-0"><path d="M18 21a8 8 0 0 0-16 0"/><circle cx="10" cy="8" r="5"/><path d="M22 20c0-3.37-2-6.5-4-8a5 5 0 0 0-.45-8.3"/></svg>
                            <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                                User
                            </span>
                        </a>
                    </li>
                    <li class="w-full px-3">
                        <a href="/admin/reports"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text-icon lucide-file-text size-6 shrink-0"><path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z"/><path d="M14 2v5a1 1 0 0 0 1 1h5"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                            <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                                Laporan
                            </span>
                        </a>
                    </li>
                    <li class="w-full px-3">
                        <a href="/rooms"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-plus-icon lucide-calendar-plus size-6 shrink-0"><path d="M16 19h6"/><path d="M16 2v4"/><path d="M19 16v6"/><path d="M21 12.598V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8.5"/><path d="M3 10h18"/><path d="M8 2v4"/></svg>                            <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                                Buat Booking
                            </span>
                        </a>
                    </li>
                <!-- User Biasa 😄 -->
                <?php else: ?>
                    <li class="w-full px-3">
                        <a href="/rooms"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-search-icon lucide-search size-6 shrink-0">
                                <path d="m21 21-4.34-4.34" />
                                <circle cx="11" cy="11" r="8" />
                            </svg>
                            <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
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
                            <path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z" />
                            <path d="M6 12h2" />
                            <path d="M6 8h2" />
                        </svg>
                        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                                Booking
                            </span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
        <!-- Bagian bawah 🤔 -->
        <ul class="flex flex-col mt-10 space-y-4 w-full px-3">
<<<<<<< HEAD
            <?php if (App::$app->auth->isGuest()): ?>
=======
            <?php if (auth()->guest()): ?>
>>>>>>> revampwf
                <!-- Empty -->
            <?php else: ?>
                <li class="mx-2 px-2">
                    <a href="/profile"
                        class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-emerald-600 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-user-round-icon lucide-user-round size-6 shrink-0">
                            <circle cx="12" cy="8" r="5" />
                            <path d="M20 21a8 8 0 0 0-16 0" />
                        </svg>
                        <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
                            Profil
                        </span>
                    </a>
                </li>
                <li class="mx-2 px-2">
                    <form action="/logout" method="post">
                        <?= csrf_field() ?>
                        <button type="submit"
                            class="flex items-center gap-4 p-3 w-full rounded-xl hover:bg-red-600 transition-all mb-4 cursor-pointer">
<<<<<<< HEAD
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out-icon lucide-log-out size-6 shrink-0"><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>                            <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
=======
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-log-out-icon lucide-log-out size-6 shrink-0"><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>                            <span class="whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2.5 group-hover:translate-x-0">
>>>>>>> revampwf
                                Logout
                            </span>
                        </button>
                    </form>
                </li>
            <?php endif; ?>
        </ul>
    </aside>
    <!-- Main Content -->
    <main class="py-12 md:pt-0 min-h-dvh pl-28">
        <div class="mx-auto bg-primary md:bg-slate-100">
            <div class="bg-white top-0 left-0 w-full h-40 fixed -z-10">

            </div>
            <!-- Flash Messages -->
            <?php if ($m = App::$app->session->getFlash('success')): ?>
                <div class="mb-6 bg-green-50 border-l-4 border-emerald-500 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-emerald-800 font-medium"><?= nl2br(htmlspecialchars($m)) ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($m = App::$app->session->getFlash('error')): ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-red-800 font-medium"><?= nl2br(htmlspecialchars($m)) ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($m = App::$app->session->getFlash('warning')): ?>
                <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-yellow-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="text-yellow-800 font-medium"><?= nl2br(htmlspecialchars($m)) ?></p>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($m = App::$app->session->getFlash('info')): ?>
                <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-blue-800 font-medium"><?= nl2br(htmlspecialchars($m)) ?></p>
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
<<<<<<< HEAD
        class="fixed left-0 top-0 right-0 bg-primary text-white w-full flex items-center justify-between px-6 py-4 z-40 md:hidden">

=======
        class="absolute left-0 top-0 right-0 bg-primary text-white w-full flex items-center justify-between px-6 py-4 z-30 md:hidden">
>>>>>>> revampwf
        <div>Logo</div>
        <div>
            <a href="#">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bell-icon lucide-bell size-6"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/></svg>
            </a>
        </div>
    </header>
    <nav class="fixed left-0 -bottom-1 right-0 bg-primary text-white md:hidden z-50 rounded-t-4xl py-3 shadow-xl">
        <div class="flex items-center justify-around w-full px-4">
            <?php
            $active = $_SERVER['REQUEST_URI'];
            $currentPageQuery = basename($active);
            $currentPage = explode('?', $currentPageQuery)[0];
<<<<<<< HEAD
            // echo "<pre>";
            // print_r($currentPage);
            // echo '</pre>';
            // exit;

            if (App::$app->auth->isGuest()) {
=======
            if (auth()->guest()) {
>>>>>>> revampwf
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
                class="flex flex-col gap-1.5 text-sm items-center <?= isActiveClass($active, $url) ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-house size-6">
                    <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" />
                    <path
                        d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-search-icon lucide-search size-6">
                        <path d="m21 21-4.34-4.34" />
                        <circle cx="11" cy="11" r="8" />
                    </svg>
                    <span>Cari</span>
                </a>
                <!-- buking -->
                <a href="/my-bookings" class="flex flex-col gap-1.5 text-sm items-center <?= isActiveClass($active, "/my-bookings") ?>">
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
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
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
<<<<<<< HEAD

<script src="src/script.js"></script>
=======
<script src="src/script.js"></script>
>>>>>>> revampwf
