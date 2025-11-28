<?php

namespace App\Core\Queue;

interface QueueJob
{
    /**
     * Execute the job
     */
    public function handle(): void;

    /**
     * Get the job name/identifier
     */
    public function getName(): string;

    /**
     * Get the job data
     */
    public function getData(): array;

    /**
     * Set the job data
     */
    public function setData(array $data): void;

    /**
     * Get max retry attempts
     */
    public function getMaxRetries(): int;
}
