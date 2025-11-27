<?php
declare(strict_types=1);

use App\Core\App;
use App\Core\Event;
use App\Models\Booking;
use App\Models\User;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

// Listen for booking created
Event::listen('booking.created', function ($booking) {
    // Send email notification
    error_log("New booking created: {$booking->id_booking}");
});

// Listen for user updated
Event::listen('users.updated', function ($user) {
    error_log("User {$user->id_user} was updated");
});

// Listen for booking deleted
Event::listen('booking.deleted', function ($booking) {
    error_log("Booking {$booking->id_booking} was deleted");
});

// Listen for user restored
Event::listen('users.restored', function ($user) {
    error_log("User {$user->id_user} was restored");
});

$root = dirname(__DIR__);

// Load environment variables
$dotenv = Dotenv::createImmutable($root);
$dotenv->safeLoad();

// Load config array
$config = require $root . '/Config/config.php';

// Error reporting per env/debug
if (($config['app']['env'] ?? 'development') === 'development' && ($config['app']['debug'] ?? true)) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Timezone
date_default_timezone_set($config['app']['timezone'] ?? 'Asia/Jakarta');

// Build the App and return it
$app = new App($root, $config);
return $app;
