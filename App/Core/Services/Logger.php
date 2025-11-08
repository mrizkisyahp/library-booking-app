<?php

namespace App\Core\Services;

use App\Core\App;

class Logger
{
    private static function getLogDir(): string
    {
        $dir = App::$ROOT_DIR . '/Storage/Logs';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        return $dir;
    }

    private static function write(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = "[{$timestamp}] [{$level}] {$message}{$contextStr}" . PHP_EOL;
        
        $logFile = self::getLogDir() . '/' . date('Y-m-d') . '.log';
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    public static function info(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }
    
    public static function error(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
        
        $errorFile = self::getLogDir() . '/error.log';
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = "[{$timestamp}] {$message}{$contextStr}" . PHP_EOL;
        file_put_contents($errorFile, $logMessage, FILE_APPEND);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::write('WARNING', $message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        if (getenv('APP_ENV') === 'development') {
            self::write('DEBUG', $message, $context);
        }
    }

    public static function auth(string $action, int $userId, string $details = ''): void
    {
        $message = "User #{$userId} {$action}";
        if ($details) {
            $message .= " - {$details}";
        }
        self::info($message, ['user_id' => $userId, 'action' => $action]);
    }

    public static function booking(string $action, int $userId, int $bookingId, array $data = []): void
    {
        $message = "Booking #{$bookingId} {$action} by User #{$userId}";
        self::info($message, array_merge(['user_id' => $userId, 'booking_id' => $bookingId], $data));
    }

    public static function admin(string $action, int $adminId, string $details = ''): void
    {
        $message = "Admin #{$adminId} {$action}";
        if ($details) {
            $message .= " - {$details}";
        }
        self::info($message, ['admin_id' => $adminId, 'action' => $action]);
    }
}
