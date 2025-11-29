<?php

namespace App\Core\Queue;

class FileQueue
{
    protected string $queuePath;

    public function __construct(string $queuePath)
    {
        $this->queuePath = rtrim($queuePath, '/');

        if (!is_dir($this->queuePath)) {
            mkdir($this->queuePath, 0755, true);
        }
    }

    /**
     * Push a job to the queue
     */
    public function push(QueueJob $job): string
    {
        $id = uniqid('job_', true);
        $filePath = $this->queuePath . '/' . $id . '.json';

        $payload = [
            'id' => $id,
            'job' => get_class($job),
            'data' => $job->getData(),
            'attempts' => 0,
            'max_retries' => $job->getMaxRetries(),
            'created_at' => time(),
        ];

        file_put_contents($filePath, json_encode($payload, JSON_PRETTY_PRINT));

        return $id;
    }

    /**
     * Get the next job from the queue
     */
    public function pop(): ?array
    {
        $files = glob($this->queuePath . '/job_*.json');

        if (empty($files)) {
            return null;
        }

        // Sort by creation time (oldest first)
        usort($files, fn($a, $b) => filemtime($a) <=> filemtime($b));

        $filePath = $files[0];
        $payload = json_decode(file_get_contents($filePath), true);

        // Remove from queue
        unlink($filePath);

        return $payload;
    }

    /**
     * Mark a job as failed
     */
    public function failed(array $payload, \Exception $exception): void
    {
        $failedPath = $this->queuePath . '/failed';

        if (!is_dir($failedPath)) {
            mkdir($failedPath, 0755, true);
        }

        $payload['failed_at'] = time();
        $payload['error'] = $exception->getMessage();
        $payload['trace'] = $exception->getTraceAsString();

        $filePath = $failedPath . '/' . $payload['id'] . '.json';
        file_put_contents($filePath, json_encode($payload, JSON_PRETTY_PRINT));
    }

    /**
     * Retry a failed job
     */
    public function retry(array $payload): void
    {
        $payload['attempts']++;

        if ($payload['attempts'] >= $payload['max_retries']) {
            return; // Max retries reached
        }

        // Remove failed job metadata
        unset($payload['failed_at'], $payload['error'], $payload['trace']);

        $filePath = $this->queuePath . '/' . $payload['id'] . '.json';
        file_put_contents($filePath, json_encode($payload, JSON_PRETTY_PRINT));
    }

    /**
     * Get queue size
     */
    public function size(): int
    {
        return count(glob($this->queuePath . '/job_*.json'));
    }
}
