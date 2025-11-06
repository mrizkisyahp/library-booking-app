<?php 
use App\Core\App;
use App\Core\Csrf;
/** @var \App\Models\User $user */ $user = App::$app->user; 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(App::$app->getTitle()) ?></title>
    <link href="<?= $basePath === '' ? '' : $basePath ?>/css/output.css" rel="stylesheet">
</head>

<body class="min-h-dvh">
    <header class="w-full bg-primary text-white flex justify-between items-center px-16 py-2 fixed">
        <a 
            <?php if (App::isGuest()): ?>
                href="/"
            <?php else: ?>
                <?php if ($user->role === 'admin'): ?>
                    href="/admin" 
                <?php else: ?>
                     href="/dashboard"
                <?php endif; ?>
            <?php endif; ?>
        
        class="font-bold">Library Booking App
        </a>
        <nav>
            <?php if (App::isGuest()): ?>
                <a href="/login">Login</a>
                <a href="/register">Register</a>
            <?php else: ?>
                <?php if ($user->role === 'admin'): ?>
                    <div class=" *:px-4">
                        <a href="/admin/bookings">Bookings</a>
                        <a href="/admin/rooms">Rooms</a>
                        <a href="/admin/users">Users</a>
                        <a href="/admin/reports">Reports</a>
                        <a href="/rooms">Book Room</a>
                    </div>
                <?php else: ?>
                    <div class="*:px-2">
                        <a href="/rooms">Rooms</a>
                        <a href="/my-bookings">My Bookings</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </nav>

        <div class="flex gap-4">
            <?php if (App::isGuest()): ?>
                <!-- Empty -->
            <?php else: ?>
                <a href="/profile">Profile</a>
                <form action="/logout" method="post">
                    <?= Csrf::field() ?>
                    <button type="submit" class=" cursor-pointer">
                        Logout
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </header>

    <main class="px-16 py-12 bg-gray-200">
        <?php if ($m = App::$app->session->getFlash('success')): ?>
            <p><?= htmlspecialchars($m) ?></p>
        <?php endif; ?>
        
        <div class="mt-4">
            {{content}}
        </div>
    </main>

    <footer class="w-full bg-primary text-center text-white text-sm font-light py-4">
        <p>&copy; <?= date('Y') ?> Library Booking App PNJ</p>
    </footer>
</body>

</html>
