<?php

/**
 * Queue Worker - CLI script for processing queued jobs
 * 
 * Usage: php Script/QueueWorker.php
 */

define('ROOT_DIR', dirname(__DIR__));

require_once ROOT_DIR . '/vendor/autoload.php';

use App\Core\Queue\Queue;
use App\Core\Dotenv;

// Load environment variables
$envFile = ROOT_DIR . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue;
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value);
    }
}

// Graceful shutdown handling (only available on Unix systems)
$shouldStop = false;

if (function_exists('pcntl_signal')) {
    if (defined('SIGTERM')) {
        pcntl_signal(SIGTERM, function () use (&$shouldStop) {
            echo "\n[" . date('Y-m-d H:i:s') . "] Received SIGTERM. Finishing current job and stopping...\n";
            $shouldStop = true;
        });
    }

    if (defined('SIGINT')) {
        pcntl_signal(SIGINT, function () use (&$shouldStop) {
            echo "\n[" . date('Y-m-d H:i:s') . "] Received SIGINT (Ctrl+C). Finishing current job and stopping...\n";
            $shouldStop = true;
        });
    }
}

echo "===========================================\n";
echo "   Queue Worker Started\n";
echo "===========================================\n";
echo "Press Ctrl+C to stop" . (function_exists('pcntl_signal') ? ' gracefully' : '') . "\n\n";

$jobsProcessed = 0;

while (!$shouldStop) {
    // Check for signals (only on Unix)
    if (function_exists('pcntl_signal_dispatch')) {
        pcntl_signal_dispatch();
    }

    // Get next job from queue
    $payload = Queue::pop();

    if ($payload === null) {
        // No jobs available, sleep for 1 second
        sleep(1);
        continue;
    }

    $jobId = $payload['id'];
    $jobClass = $payload['job'];
    $attempts = $payload['attempts'] + 1;

    echo "[" . date('Y-m-d H:i:s') . "] Processing job: {$jobClass} (ID: {$jobId}, Attempt: {$attempts})\n";

    try {
        // Process the job
        Queue::process($payload);

        $jobsProcessed++;
        echo "[" . date('Y-m-d H:i:s') . "] ✓ Job completed successfully\n";

    } catch (\Exception $e) {
        echo "[" . date('Y-m-d H:i:s') . "] ✗ Job failed: " . $e->getMessage() . "\n";

        // Update attempts
        $payload['attempts'] = $attempts;

        // Check if we should retry
        if ($attempts < $payload['max_retries']) {
            echo "[" . date('Y-m-d H:i:s') . "] → Retrying job (attempt {$attempts}/{$payload['max_retries']})\n";
            Queue::retry($payload);
        } else {
            echo "[" . date('Y-m-d H:i:s') . "] → Max retries reached. Moving to failed queue.\n";
            Queue::failed($payload, $e);
        }
    }

    echo "\n";
}

echo "\n===========================================\n";
echo "   Queue Worker Stopped\n";
echo "   Jobs Processed: {$jobsProcessed}\n";
echo "===========================================\n";
