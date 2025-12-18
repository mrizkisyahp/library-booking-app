<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\DashboardService;
use App\Services\BookingService;

class UserDashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
        private BookingService $bookingService,
    ) {
    }
    public function index(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Dashboard | Library Booking App');

        $data = $this->dashboardService->getUserDashboardData(user()->id_user);

        $bookings = $data['bookings'];
        $user = $data['user'];
        $feedbacks = $data['pendingFeedbacks'];
        $pendingInvitations = $this->bookingService->getPendingForUser($user->id_user);
        $myJoinRequests = $this->bookingService->getMyPendingJoinRequests($user->id_user);

        $data = $this->dashboardService->getUserDashboardData(user()->id_user);

        // return view('User/UserDashboard', [
        //     'user' => $data['user'],
        //     'bookings' => $data['bookings'],
        //     'feedbacks' => $data['pendingFeedbacks'],
        //     'bookingStats' => $data['bookingStats'],
        //     'pendingInvitations' => $pendingInvitations,
        //     'myJoinRequests' => $myJoinRequests,
        // ]);


        return view('User/UserDashboard', [
            'bookings' => $bookings,
            'user' => $user,
            'feedbacks' => $feedbacks,
            'pendingInvitations' => $pendingInvitations,
            'myJoinRequests' => $myJoinRequests,
        ]);
    }
}
