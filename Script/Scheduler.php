<?php

/**
 * Scheduler - CLI script for running scheduled tasks
 * 
 * Usage: php Script/Scheduler.php
 * 
 * Windows Task Scheduler:
 *   schtasks /create /sc daily /tn "LibraryScheduler" /tr "php c:\xampp\htdocs\PBL\library-booking-app\Script\Scheduler.php" /st 00:01
 * 
 * Linux Cron (production):
 *   1 0 * * * php /path/to/library-booking-app/Script/Scheduler.php >> /var/log/scheduler.log 2>&1
 */

declare(strict_types=1);

define('ROOT_DIR', dirname(__DIR__));

require_once ROOT_DIR . '/vendor/autoload.php';
$app = require_once ROOT_DIR . '/Bootstrap/App.php';

use App\Core\Repository\UserRepository;
use App\Core\App;

// Logging helper
function logMessage(string $message): void
{
    $logDir = ROOT_DIR . '/Storage/Logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $logFile = $logDir . '/scheduler.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[{$timestamp}] {$message}\n", FILE_APPEND);
    echo "[{$timestamp}] {$message}\n";
}

logMessage("Scheduler started");

// ==================== SCHEDULED TASKS ====================

// Task 1: Deactivate expired users
try {
    $userRepo = new UserRepository(App::$app->db);
    $count = $userRepo->deactivateExpiredUsers();
    logMessage("ExpireUsers: {$count} users deactivated");
} catch (Exception $e) {
    logMessage("ExpireUsers ERROR: " . $e->getMessage());
}

// Add more scheduled tasks here as needed...

logMessage("Scheduler completed");
