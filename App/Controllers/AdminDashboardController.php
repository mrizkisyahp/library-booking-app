<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Request;
use App\Services\DashboardService;
class AdminDashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {
    }
    public function index(Request $request)
    {
        $data = $this->dashboardService->getAdminDashboardData();
        return $this->render('Admin/AdminDashboard', $data);
    }

    public function settings(Request $request)
    {
        return $this->render('Admin/Settings/Index');
    }
}