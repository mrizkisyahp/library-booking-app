<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Services\DashboardService;

class UserDashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService
    ) {
    }
    public function index(Request $request)
    {
        $data = $this->dashboardService->getUserDashboardData(user()->id_user);

        return $this->render('User/UserDashboard', $data);
    }
}
