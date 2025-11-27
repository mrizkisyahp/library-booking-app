<?php

namespace App\Jobs;

use App\Core\Queue\Job;

/**
 * Example job for testing the queue system
 */
class ExampleJob extends Job
{
    protected int $maxRetries = 2;

    public function handle(): void
    {
        $name = $this->data['name'] ?? 'World';

        // Simulate some work
        echo "Hello, {$name}! Processing job...\n";
        sleep(2);
        echo "Job completed!\n";

        // Example: You could send an email, process images, etc.
        // For testing, we just log to Storage/Logs
        $logFile = dirname(__DIR__, 2) . '/Storage/Logs/queue.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $message = date('Y-m-d H:i:s') . " - ExampleJob executed with name: {$name}\n";
        file_put_contents($logFile, $message, FILE_APPEND);
    }
}
