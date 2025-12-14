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
        $this->setLayout('main');
        $this->setTitle('Dashboard Admin | Library Booking App');

        $data = $this->dashboardService->getAdminDashboardData();
        return $this->render('Admin/AdminDashboard', $data);
    }

    public function settings(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Pengaturan | Library Booking App');

        $libraryClosedToday = libraryIsClosedToday();
        $closureReason = $libraryClosedToday ? getClosureReason(date('Y-m-d')) : null;

        return $this->render('Admin/Settings/Index', [
            'libraryClosedToday' => $libraryClosedToday,
            'closureReason' => $closureReason,
        ]);
    }
}