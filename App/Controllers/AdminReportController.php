<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\AdminReportService;
use App\Core\Middleware\AdminMiddleware;

class AdminReportController extends Controller
{
    public function __construct(
        private AdminReportService $service
    ) {
    }

    public function index(Request $request, Response $response)
    {
        $this->setLayout('main');
        $this->setTitle('Report | Library Booking App');

        $params = array_merge($request->getQuery(), $request->getBody());

        $filters = [
            'start_date' => $params['start_date'] ?? '',
            'end_date' => $params['end_date'] ?? '',
            'status' => $params['status'] ?? '',
            'chart_type' => $params['chart_type'] ?? 'booking',
        ];

        $summary = $this->service->getSummary($filters);
        $chartData = $this->service->getChartData($filters);
        $reportRows = $this->service->getTableData($filters);

        return $this->render('Admin/Report', [
            'filters' => $filters,
            'summary' => $summary,
            'chartData' => $chartData,
            'reportRows' => $reportRows,
            'chartType' => $filters['chart_type'],
        ]);
    }
}
