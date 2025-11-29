<?php

namespace App\Core\Queue;

abstract class Job implements QueueJob
{
    protected array $data = [];
    protected int $maxRetries = 3;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getName(): string
    {
        return static::class;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    abstract public function handle(): void;
}
