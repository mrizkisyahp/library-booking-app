<?php
use App\Core\App;
use App\Core\Csrf;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(App::$app->getTitle()) ?></title>
    <link href="<?= './css/output.css' ?>" rel="stylesheet">
</head>
<body class="min-h-dvh">
    <header>
        <h1>Library Booking App</h1>
        <nav>
            <?php if (App::isGuest()): ?>
                <a href="/">Home</a> |
                <a href="/login">Login</a> |
                <a href="/register">Register</a>
            <?php else: ?>
                <?php $user = App::$app->user; ?>
                <?php if ($user->role === 'admin'): ?>
                    <a href="/admin">Admin Dashboard</a> |
                    <a href="/admin/bookings">Manage Bookings</a> |
                    <a href="/admin/rooms">Manage Rooms</a> |
                    <a href="/admin/users">Manage Users</a> |
                    <a href="/admin/reports">Reports</a> |
                    <a href="/rooms">Book Room</a> |
                <?php else: ?>
                    <a href="/dashboard">Dashboard</a> |
                    <a href="/rooms">Rooms</a> |
                    <a href="/my-bookings">My Bookings</a> |
                <?php endif; ?>
                <a href="/profile">Profile</a> |
                <form action="/logout" method="post">
                    <?= Csrf::field() ?>
                    <button type="submit">Logout</button>
                </form>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <?php if ($m = App::$app->session->getFlash('success')): ?>
            <p><?= htmlspecialchars($m) ?></p>
        <?php endif; ?>

        {{content}}
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Library Booking App PNJ</p>
    </footer>
</body>
</html>