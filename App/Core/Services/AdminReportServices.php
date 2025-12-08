<?php

namespace App\Core\Services;

use App\Core\Repository\AdminReportRepository;

class AdminReportServices
{
    private AdminReportRepository $repo;

    public function __construct()
    {
        $this->repo = new AdminReportRepository();
    }

    /**
     * SUMMARY untuk box ringkasan
     */
    public function getSummary(array $filters): array
    {
        $summary = $this->repo->fetchSummary($filters);

        return [
            'total'     => (int)($summary['total'] ?? 0),
            'completed' => (int)($summary['completed'] ?? 0),
            'cancelled' => (int)($summary['cancelled'] ?? 0),
        ];
    }

    /**
     * Data Chart.js
     */
    public function getChartData(array $filters): array
    {
        $rows = $this->repo->fetchChartData($filters);

        return [
            'labels' => array_column($rows, 'date'),
            'values' => array_column($rows, 'total'),
        ];
    }

    /**
     * Data tabel
     */
    public function getTableData(array $filters): array
    {
        return $this->repo->fetchTableData($filters);
    }
}
