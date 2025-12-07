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
        // dd("Hello");
        $data = $this->dashboardService->getUserDashboardData(user()->id_user);

        $bookings = $data['bookings'];
        $user = $data['user'];
        $feedbacks = $data['pendingFeedbacks'];

        return view('User/UserDashboard', [
            'bookings' => $bookings,
            'user' => $user,
            'feedbacks' => $feedbacks,
        ]);
    }
}
