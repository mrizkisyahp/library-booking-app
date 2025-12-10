<?php
namespace App\Core\Services;

use App\Core\Repository\AdminReportRepository;

class AdminReportServices
{
    private AdminReportRepository $repo;

    public function __construct(?AdminReportRepository $repo = null)
    {
        $this->repo = $repo ?? new AdminReportRepository();
    }

    public function getSummary(array $filters): array
    {
        return $this->repo->fetchSummary($filters);
    }

    public function getChartData(array $filters): array
    {
        $chartType = $filters['chart_type'] ?? 'booking';

        switch ($chartType) {
            case 'room':
                return $this->repo->fetchTopRooms($filters);

            case 'feedback':
                return $this->repo->fetchTopFeedback($filters);

            case 'hours':
                return $this->repo->fetchBusyHours($filters);

            case 'booking':
            default:
                return $this->repo->fetchBookingCountsByDate($filters);
        }
    }

    public function getTableData(array $filters): array
    {
        return $this->repo->fetchReportRows($filters);
    }
}
