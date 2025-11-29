<?php

namespace App\Core\Queue;

use App\Core\App;

class Queue
{
    protected static ?FileQueue $driver = null;

    /**
     * Get the queue driver instance
     */
    protected static function getDriver(): FileQueue
    {
        if (self::$driver === null) {
            $queuePath = dirname(__DIR__, 2) . '/Storage/Queue';
            self::$driver = new FileQueue($queuePath);
        }

        return self::$driver;
    }

    /**
     * Push a job to the queue
     */
    public static function push(QueueJob $job): string
    {
        return self::getDriver()->push($job);
    }

    /**
     * Get the next job from the queue
     */
    public static function pop(): ?array
    {
        return self::getDriver()->pop();
    }

    /**
     * Mark a job as failed
     */
    public static function failed(array $payload, \Exception $exception): void
    {
        self::getDriver()->failed($payload, $exception);
    }

    /**
     * Retry a failed job
     */
    public static function retry(array $payload): void
    {
        self::getDriver()->retry($payload);
    }

    /**
     * Get queue size
     */
    public static function size(): int
    {
        return self::getDriver()->size();
    }

    /**
     * Process a job payload
     */
    public static function process(array $payload): void
    {
        $jobClass = $payload['job'];

        if (!class_exists($jobClass)) {
            throw new \Exception("Job class {$jobClass} does not exist");
        }

        $job = new $jobClass($payload['data']);

        if (!$job instanceof QueueJob) {
            throw new \Exception("Job must implement QueueJob interface");
        }

        $job->handle();
    }
}
