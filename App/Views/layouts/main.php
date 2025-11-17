<?php
use App\Core\App;
use App\Core\Csrf;
/** @var \App\Models\User|null $user */
$user = App::$app->user;
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

<body class="min-h-screen bg-linear-to-br from-slate-50 to-slate-100">
    <!-- Header -->
    <header class="hidden md:flex fixed w-full bg-primary text-white shadow-lg top-0 left-0 right-0 z-50">
        <div class="w-full mx-auto px-8 py-4 flex justify-between items-center">
            <!-- Logo -->
            <a <?php if (App::$app->auth->isGuest()): ?> href="/" <?php else: ?> 
                    <?php if ($user && $user->isAdmin()): ?>
                        href="/admin" 
                    <?php else: ?>
                         href="/dashboard"
                    <?php endif; ?>
                <?php endif; ?>
                class="text-2xl font-bold hover:opacity-90 transition-opacity flex items-center gap-2 shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Library Booking
            </a>
            <!-- Navigation -->
            <nav class="flex items-center gap-1 grow justify-center">
                <?php if (App::$app->auth->isGuest()): ?>
                    <a href="/login" class="px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors">Login</a>
                    <a href="/register"
                        class="px-4 py-2 rounded-lg bg-white text-primary hover:bg-gray-100 transition-colors font-semibold">Register</a>
                <?php else: ?>
                    <?php if ($user && $user->isAdmin()): ?>
                        <a href="/admin/bookings" class="px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors">Manage
                            Bookings</a>
                        <a href="/admin/rooms" class="px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors">Manage
                            Rooms</a>
                        <a href="/admin/users" class="px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors">Manage
                            Users</a>
                        <a href="/admin/reports" class="px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors">Reports</a>
                        <a href="/rooms" class="px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors">Book Room</a>
                    <?php else: ?>
                        <a href="/rooms" class="px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors">Rooms</a>
                        <a href="/my-bookings" class="px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors">My
                            Bookings</a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>

            <!-- User Menu -->
            <div class="flex items-center gap-3 shrink-0">
                <?php if (App::$app->auth->isGuest()): ?>
                    <!-- Empty -->
                <?php else: ?>
                    <a href="/profile"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-emerald-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profile
                    </a>
                    <form action="/logout" method="post">
                        <?= Csrf::field() ?>
                        <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-emerald-700 active:bg-rose-600 transition-colors cursor-pointer focus:outline-none focus:ring-2 focus:bg-rose-600 focus:ring-rose-500 focus:ring-offset-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pt-12 md:pt-24 pb-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-6">
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
    <footer class="bg-primary text-white py-8 mt-auto">
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
    <?php if (App::$app->auth->isGuest()): ?>
    <div class="min-h-dvh bg-black/30 backdrop-blur-md z-50">
        <div class="bg-white shadow-lg fixed flex justify-center items-center">
            <a href="/login">Login</a>
            <a href="/register">Register</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- mobile Navigation -->
    <nav
        class="fixed left-0 bottom-0 right-0 bg-primary py-2 text-white shadow-lg flex items-center justify-around md:hidden z-50">
        <?php 
            $active = $_SERVER['REQUEST_URI']; 
            
            if (App::$app->auth->isGuest()) {
                $url = '/';
            } else {
                $url = $user && $user->isAdmin() ? '/admin' : '/dashboard';
            }

            function isActive($current, $target) {
                return $current === $target 
                ? "bg-emerald-600 rounded-lg"
                : "hover:bg-emerald-600 rounded-lg"
                ;
            }
        ?>
        
        <a href="<?= $url ?> "
            class="flex flex-col text-sm items-center transition-all px-2 py-1 <?= isActive($active, $url)  ?>"
            >
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-house-icon lucide-house">
                <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" />
                <path
                    d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
            </svg>
            <span>
                Beranda
            </span>
        </a>
    
        <?php if ($user && $user->isAdmin()): ?>
            <a href="/admin/bookings" class="flex flex-col text-sm items-center transition-all px-2 py-1 <?= isActive($active, "/admin/bookings")  ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-square-chart-gantt-icon lucide-square-chart-gantt">
                    <rect width="18" height="18" x="3" y="3" rx="2" />
                    <path d="M9 8h7" />
                    <path d="M8 12h6" />
                    <path d="M11 16h5" />
                </svg>
                <span>
                    Manajemen
                </span>
            </a>
    
            <a href="/reports" class="flex flex-col text-sm items-center transition-all px-2 py-1 <?= isActive($active, "/reports")  ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-door-open-icon lucide-door-open">
                    <path d="M11 20H2" />
                    <path d="M11 4.562v16.157a1 1 0 0 0 1.242.97L19 20V5.562a2 2 0 0 0-1.515-1.94l-4-1A2 2 0 0 0 11 4.561z" />
                    <path d="M11 4H8a2 2 0 0 0-2 2v14" />
                    <path d="M14 12h.01" />
                    <path d="M22 20h-3" />
                </svg>
                <span>
                    Laporan
                </span>
            </a>
        <?php else: ?>
            <a href="/my-bookings" class="flex flex-col text-sm items-center transition-all px-2 py-1 <?= isActive($active, "/my-bookings")  ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-square-chart-gantt-icon lucide-square-chart-gantt">
                    <rect width="18" height="18" x="3" y="3" rx="2" />
                    <path d="M9 8h7" />
                    <path d="M8 12h6" />
                    <path d="M11 16h5" />
                </svg>
                <span>
                    Booking saya
                </span>
            </a>
    
            <a href="/rooms" class="flex flex-col text-sm items-center transition-all px-2 py-1 <?= isActive($active, "/rooms")  ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-door-open-icon lucide-door-open">
                    <path d="M11 20H2" />
                    <path d="M11 4.562v16.157a1 1 0 0 0 1.242.97L19 20V5.562a2 2 0 0 0-1.515-1.94l-4-1A2 2 0 0 0 11 4.561z" />
                    <path d="M11 4H8a2 2 0 0 0-2 2v14" />
                    <path d="M14 12h.01" />
                    <path d="M22 20h-3" />
                </svg>
                <span>
                    Ruangan
                </span>
            </a>
        <?php endif; ?>

        <a href="/profile" class="flex flex-col text-sm items-center transition-all px-2 py-1 <?= isActive($active, "/profile")  ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-icon lucide-user"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>
                Profil
            </span>
        </a>
    </nav>
</body>

</html>

<script src="src/script.js"></script>