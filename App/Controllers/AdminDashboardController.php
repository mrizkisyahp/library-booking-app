<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Middleware\AdminMiddleware;
use App\Core\Services\AdminDashboardService;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware());
    }

    public function index()
    {
        $dashboardService = new AdminDashboardService();
        $this->setTitle('Admin Dashboard | Library Booking App');
        $this->setLayout('main');

        $stats = $dashboardService->getGlobalStatistics();
        $recentBookings = $dashboardService->getRecentBookings();
        $roomUsage = $dashboardService->getRoomUsage();

        return $this->render('Admin/AdminDashboard', [
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'roomUsage' => $roomUsage
        ]);
    }
}
