<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Services\DashboardService;
use App\Core\Services\BookingServices;

class UserDashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
        private BookingServices $bookingServices,
    ) {
    }
    public function index(Request $request)
    {
        // dd("Hello");
        $data = $this->dashboardService->getUserDashboardData(user()->id_user);

        $bookings = $data['bookings'];
        $user = $data['user'];
        $feedbacks = $data['pendingFeedbacks'];
        $pendingInvitations = $this->bookingServices->getPendingForUser($user->id_user);
        $myJoinRequests = $this->bookingServices->getMyPendingJoinRequests($user->id_user);

        return view('User/UserDashboard', [
            'bookings' => $bookings,
            'user' => $user,
            'feedbacks' => $feedbacks,
            'pendingInvitations' => $pendingInvitations,
            'myJoinRequests' => $myJoinRequests,
        ]);
    }
}
