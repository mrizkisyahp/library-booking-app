<?php
declare(strict_types=1);

use App\Core\Services\BookingExpirationService;

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../Bootstrap/App.php';

$service = new BookingExpirationService();
$result = $service->expireDraftBookings();

echo 'Expired bookings: ' . ($result['expired_count'] ?? 0) . PHP_EOL;
