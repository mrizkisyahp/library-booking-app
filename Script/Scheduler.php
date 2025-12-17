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

use App\Core\App;
use App\Repositories\UserRepository;
use App\Repositories\BookingRepository;
use App\Repositories\WarningRepository;
use App\Repositories\SuspensionRepository;
use App\Services\UserService;
use App\Services\BookingService;
use App\Services\EmailService;
use App\Services\Logger;
use Carbon\Carbon;

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

// Get services from container
$userRepo = App::$app->container->make(UserRepository::class);
$bookingRepo = App::$app->container->make(BookingRepository::class);
$warningRepo = App::$app->container->make(WarningRepository::class);
$suspensionRepo = App::$app->container->make(SuspensionRepository::class);
$userService = App::$app->container->make(UserService::class);
$bookingService = App::$app->container->make(BookingService::class);
$emailService = App::$app->container->make(EmailService::class);
$logger = App::$app->container->make(Logger::class);

// ==================== SCHEDULED TASKS ====================

// Task 1: Deactivate expired users
try {
    $count = $userRepo->deactivateExpiredUsers();
    logMessage("ExpireUsers: {$count} users deactivated");
} catch (Exception $e) {
    logMessage("ExpireUsers ERROR: " . $e->getMessage());
}

// Task 2: Detect no-shows and apply warnings to PIC + ALL members
try {
    $sql = "
        SELECT b.id_booking, b.user_id, b.tanggal_penggunaan_ruang, b.waktu_mulai
        FROM booking b
        WHERE b.status = 'verified'
        AND CONCAT(b.tanggal_penggunaan_ruang, ' ', b.waktu_mulai) < DATE_SUB(NOW(), INTERVAL 10 MINUTE)
    ";

    $stmt = App::$app->db->pdo->query($sql);
    $noShowBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $count = 0;
    foreach ($noShowBookings as $booking) {
        // Transition to no_show
        $bookingService->transitionTo($booking['id_booking'], 'no_show', 'No show - tidak check-in dalam 10 menit');

        // Get all members + PIC
        $members = $bookingRepo->getBookingMembers($booking['id_booking'], 1, 999);
        $allUserIds = array_column($members->items, 'id_user');
        $allUserIds[] = $booking['user_id']; // Add PIC

        // Get warning type ID for no-show
        $warningType = $warningRepo->getWarningTypeByName('No Show') ?? $warningRepo->getWarningTypeByName('Peringatan');
        $peringatanId = $warningType['id_peringatan'] ?? 1;

        // Apply warning to ALL (PIC + members)
        foreach (array_unique($allUserIds) as $userId) {
            try {
                $userService->addWarning($userId, $peringatanId, "No-show: Booking #{$booking['id_booking']}");
            } catch (Exception $e) {
                $logger->error('Failed to apply no-show warning', [
                    'booking_id' => $booking['id_booking'],
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $count++;
    }

    logMessage("DetectNoShows: {$count} bookings marked as no-show");
} catch (Exception $e) {
    logMessage("DetectNoShows ERROR: " . $e->getMessage());
}

// Task 3: Cleanup stale bookings
try {
    $deletedDrafts = 0;
    $expiredPending = 0;

    // Delete draft bookings older than 7 days
    $sql = "
        UPDATE booking 
        SET deleted_at = NOW()
        WHERE status = 'draft'
        AND created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)
        AND deleted_at IS NULL
    ";
    $stmt = App::$app->db->pdo->query($sql);
    $deletedDrafts = $stmt->rowCount();

    // Expire pending bookings where date has passed
    $sql = "
        SELECT id_booking 
        FROM booking 
        WHERE status = 'pending'
        AND tanggal_penggunaan_ruang < CURDATE()
    ";
    $stmt = App::$app->db->pdo->query($sql);
    $expiredBookings = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($expiredBookings as $bookingId) {
        try {
            $bookingService->transitionTo($bookingId, 'expired', 'Expired - tanggal sudah lewat');
            $expiredPending++;
        } catch (Exception $e) {
            $logger->error('Failed to expire booking', ['booking_id' => $bookingId, 'error' => $e->getMessage()]);
        }
    }

    logMessage("CleanupBookings: {$deletedDrafts} drafts deleted, {$expiredPending} pending expired");
} catch (Exception $e) {
    logMessage("CleanupBookings ERROR: " . $e->getMessage());
}

// Task 4: Auto-suspend users with 3+ warnings
try {
    $usersWithWarnings = $warningRepo->getUsersWithWarningCount(3);
    $suspendedCount = 0;

    foreach ($usersWithWarnings as $userData) {
        try {
            $userService->suspendUser($userData['id_akun']);
            $suspensionRepo->create($userData['id_akun'], date('Y-m-d'));
            $suspendedCount++;

            $logger->warning('User auto-suspended by cron', [
                'user_id' => $userData['id_akun'],
                'warning_count' => $userData['warning_count']
            ]);
        } catch (Exception $e) {
            $logger->error('Failed to auto-suspend user', [
                'user_id' => $userData['id_akun'],
                'error' => $e->getMessage()
            ]);
        }
    }

    logMessage("AutoSuspend: {$suspendedCount} users suspended");
} catch (Exception $e) {
    logMessage("AutoSuspend ERROR: " . $e->getMessage());
}

// Task 5: Auto-unsuspend users after 7 days, remove all warnings, reset peringatan
try {
    // Find users suspended for >= 7 days (based on suspensi_terakhir field)
    $sql = "
        SELECT id_user, nama, email 
        FROM users 
        WHERE status = 'suspended' 
        AND suspensi_terakhir IS NOT NULL
        AND suspensi_terakhir <= CURDATE()
    ";

    $stmt = App::$app->db->pdo->query($sql);
    $usersToUnsuspend = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $unsuspendedCount = 0;
    foreach ($usersToUnsuspend as $userData) {
        try {
            $userId = $userData['id_user'];

            // 1. Soft delete all warnings for this user
            $stmt = App::$app->db->prepare("
                UPDATE peringatan_mhs 
                SET deleted_at = NOW() 
                WHERE id_akun = :user_id AND deleted_at IS NULL
            ");
            $stmt->execute([':user_id' => $userId]);

            // 2. Reset users.peringatan to 0 and set status to active
            $stmt = App::$app->db->prepare("
                UPDATE users 
                SET peringatan = 0, status = 'active', suspensi_terakhir = NULL 
                WHERE id_user = :user_id
            ");
            $stmt->execute([':user_id' => $userId]);

            $unsuspendedCount++;

            $logger->info('User auto-unsuspended after 7 days', [
                'user_id' => $userId,
                'name' => $userData['nama'],
            ]);
        } catch (Exception $e) {
            $logger->error('Failed to auto-unsuspend user', [
                'user_id' => $userData['id_user'],
                'error' => $e->getMessage(),
            ]);
        }
    }

    logMessage("AutoUnsuspend: {$unsuspendedCount} users unsuspended, warnings cleared");
} catch (Exception $e) {
    logMessage("AutoUnsuspend ERROR: " . $e->getMessage());
}

// Task 6: Send email reminders (1 day and 1 hour before)
try {
    $oneDayReminders = 0;
    $oneHourReminders = 0;

    // 1 day before (tomorrow between 08:00-09:00)
    $sql = "
        SELECT b.*, u.id_user, u.email, u.nama
        FROM booking b
        LEFT JOIN users u ON b.user_id = u.id_user
        WHERE b.status = 'verified'
        AND b.tanggal_penggunaan_ruang = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
        AND b.waktu_mulai BETWEEN '08:00:00' AND '09:00:00'
    ";

    $stmt = App::$app->db->pdo->query($sql);
    $tomorrowBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($tomorrowBookings as $booking) {
        try {
            $user = new \App\Models\User();
            foreach ($booking as $key => $value) {
                if (property_exists($user, $key)) {
                    $user->$key = $value;
                }
            }

            $emailService->sendCheckInReminder($user, (object) $booking);
            $oneDayReminders++;
        } catch (Exception $e) {
            $logger->error('Failed to send 1-day reminder', [
                'booking_id' => $booking['id_booking'],
                'error' => $e->getMessage()
            ]);
        }
    }

    // 1 hour before
    $sql = "
        SELECT b.*, u.id_user, u.email, u.nama
        FROM booking b
        LEFT JOIN users u ON b.user_id = u.id_user
        WHERE b.status = 'verified'
        AND CONCAT(b.tanggal_penggunaan_ruang, ' ', b.waktu_mulai) 
            BETWEEN DATE_ADD(NOW(), INTERVAL 50 MINUTE) 
            AND DATE_ADD(NOW(), INTERVAL 70 MINUTE)
    ";

    $stmt = App::$app->db->pdo->query($sql);
    $upcomingBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($upcomingBookings as $booking) {
        try {
            $user = new \App\Models\User();
            foreach ($booking as $key => $value) {
                if (property_exists($user, $key)) {
                    $user->$key = $value;
                }
            }

            $emailService->sendCheckInReminder($user, (object) $booking);
            $oneHourReminders++;
        } catch (Exception $e) {
            $logger->error('Failed to send 1-hour reminder', [
                'booking_id' => $booking['id_booking'],
                'error' => $e->getMessage()
            ]);
        }
    }

    logMessage("EmailReminders: {$oneDayReminders} daily, {$oneHourReminders} hourly sent");
} catch (Exception $e) {
    logMessage("EmailReminders ERROR: " . $e->getMessage());
}

logMessage("Scheduler completed");
