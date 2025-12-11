<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\AdminReportService;
use App\Core\Middleware\AdminMiddleware;

class AdminReportController extends Controller
{
    private AdminReportService $service;

    public function __construct(AdminReportService $service)
    {
        $this->registerMiddleware(new AdminMiddleware());
        $this->service = $service;
    }

    public function index(Request $request, Response $response)
    {
        $this->setLayout('main');
        $this->setTitle('Report | Library Booking App');

        // $params = $request->getBody();
        // $params = $request->getQuery();
        $params = array_merge($request->getQuery(), $request->getBody());
        $chartType = isset($params['chart_type']) ? (string) $params['chart_type'] : 'booking';

        /** FILTERS */
        $filters = [
            'start_date' => $params['start_date'] ?? '',
            'end_date' => $params['end_date'] ?? '',
            'status' => $params['status'] ?? '',
            'chart_type' => isset($params['chart_type']) ? (string) $params['chart_type'] : 'booking',
        ];

        /** QUERY KE SERVICE */
        $summary = $this->service->getSummary($filters);
        $chartData = $this->service->getChartData($filters);
        $reportRows = $this->service->getTableData($filters);


        return $this->render('Admin/Report', [
            'filters' => $filters,
            'summary' => $summary,
            'chartData' => $chartData,
            'reportRows' => $reportRows,
            'chartType' => $filters['chart_type']
        ]);
    }
}
