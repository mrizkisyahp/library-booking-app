<?php

namespace App\Core;

class Paginator
{
    public function __construct(public array $items, public int $total, public int $perPage, public int $currentPage, public int $lastPage)
    {
    }

    public function hasMorePages(): bool
    {
        return $this->currentPage < $this->lastPage;
    }

    public function nextPageUrl(): ?string
    {
        if (!$this->hasMorePages()) {
            return null;
        }

        return $this->buildUrl($this->currentPage + 1);
    }

    public function prevPageUrl(): ?string
    {
        if ($this->currentPage <= 1) {
            return null;
        }

        return $this->buildUrl($this->currentPage - 1);
    }

    protected function buildUrl(int $page): string
    {
        $query = $_GET;
        $query['page'] = $page;
        return $_SERVER['REQUEST_URI'] . '?' . http_build_query($query);
    }

    public function toArray(): array
    {
        return [
            'items' => $this->items,
            'total' => $this->total,
            'per_page' => $this->perPage,
            'current_page' => $this->currentPage,
            'last_page' => $this->lastPage,
            'next_page_url' => $this->nextPageUrl(),
            'prev_page_url' => $this->prevPageUrl(),
        ];
    }
}
