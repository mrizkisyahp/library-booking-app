<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Middleware\AdminMiddleware;
use App\Core\Services\AdminReportServices;

class AdminReportController extends Controller
{
    private AdminReportServices $service;

    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware());
        $this->service = new AdminReportServices();
    }

    public function index(Request $request, Response $response)
    {
        $filters = [
            'start_date' => $request->getBody()['start_date'] ?? null,
            'end_date'   => $request->getBody()['end_date'] ?? null,
            'status'     => $request->getBody()['status'] ?? null,
        ];

        $summary   = $this->service->getSummary($filters);
        $chartData = $this->service->getChartData($filters);
        $tableData = $this->service->getTableData($filters);

        $this->setLayout('main');
        $this->setTitle('Report | Library Booking App');

        return $this->render('Admin/Report', [
            'filters'   => $filters,
            'summary'   => $summary,
            'chartData' => $chartData,
            'tableData' => $tableData,
        ]);
    }
}
