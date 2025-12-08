<?php

namespace App\Core\Services;

use App\Core\App;

class Logger
{
    private string $logDir;

    public function __construct(?string $logDir = null)
    {
        $this->logDir = $logDir ?? App::$ROOT_DIR . '/Storage/Logs';
    }

    private function getLogDir(): string
    {
        $dir = $this->logDir;
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        return $dir;
    }

    private function write(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = "[{$timestamp}] [{$level}] {$message}{$contextStr}" . PHP_EOL;

        $logFile = $this->getLogDir() . '/' . date('Y-m-d') . '.log';
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    public function info(string $message, array $context = []): void
    {
        $this->write('INFO', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('ERROR', $message, $context);

        $errorFile = $this->getLogDir() . '/error.log';
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = "[{$timestamp}] {$message}{$contextStr}" . PHP_EOL;
        file_put_contents($errorFile, $logMessage, FILE_APPEND);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->write('WARNING', $message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        if (getenv('APP_ENV') === 'development') {
            $this->write('DEBUG', $message, $context);
        }
    }

    public function auth(string $action, int $userId, string $details = ''): void
    {
        $message = "User #{$userId} {$action}";
        if ($details) {
            $message .= " - {$details}";
        }
        $this->info($message, ['user_id' => $userId, 'action' => $action]);
    }

    public function booking(string $action, int $userId, int $bookingId, array $data = []): void
    {
        $message = "Booking #{$bookingId} {$action} by User #{$userId}";
        $this->info($message, array_merge(['user_id' => $userId, 'booking_id' => $bookingId], $data));
    }

    public function admin(string $action, int $adminId, string $details = ''): void
    {
        $message = "Admin #{$adminId} {$action}";
        if ($details) {
            $message .= " - {$details}";
        }
        $this->info($message, ['admin_id' => $adminId, 'action' => $action]);
    }
}
