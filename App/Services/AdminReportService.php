<?php
namespace App\Services;

use App\Repositories\AdminReportRepository;

class AdminReportService
{
    private AdminReportRepository $repo;

    public function __construct(AdminReportRepository $repo)
    {
        $this->repo = $repo;
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

            case 'department':
                return $this->repo->fetchBookingsByDepartment($filters);

            case 'reason':
                return $this->repo->fetchBookingsByReason($filters);

            case 'daily':
                return $this->repo->fetchBookingsByDay($filters);

            case 'weekly':
                return $this->repo->fetchBookingsByWeek($filters);

            case 'monthly':
                return $this->repo->fetchBookingsByMonth($filters);

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
