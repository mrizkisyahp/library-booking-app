<?php

namespace App\Services;

use App\Repositories\AdminReportRepository;

class AdminReportService
{
    public function __construct(
        private AdminReportRepository $repo
    ) {
    }

    /**
     * Get summary statistics
     */
    public function getSummary(array $filters): array
    {
        return $this->repo->fetchSummary($filters);
    }

    /**
     * Get chart data based on chart type
     */
    public function getChartData(array $filters): array
    {
        $chartType = $filters['chart_type'] ?? 'day';

        return match ($chartType) {
            'day' => $this->repo->fetchTotalByPeriod($filters, 'day'),
            'week' => $this->repo->fetchTotalByPeriod($filters, 'week'),
            'month' => $this->repo->fetchTotalByPeriod($filters, 'month'),
            'semester' => $this->repo->fetchTotalByPeriod($filters, 'semester'),
            'year' => $this->repo->fetchTotalByPeriod($filters, 'year'),
            'room' => $this->repo->fetchFavoriteRooms($filters),
            'department' => $this->repo->fetchByDepartment($filters),
            'purpose' => $this->repo->fetchByPurpose($filters),
            'hours' => $this->repo->fetchBusyHours($filters),
            default => $this->repo->fetchTotalByPeriod($filters, 'day'),
        };
    }

    /**
     * Get detailed table data for display/export
     */
    public function getTableData(array $filters): array
    {
        return $this->repo->fetchReportRows($filters);
    }
}
