<?php

namespace App\Services;

use App\Repositories\BookingRepository;
use App\Repositories\UserRepository;
use App\Repositories\RoomRepository;

class DashboardService
{
    public function __construct(
        private BookingRepository $bookingRepo,
        private UserRepository $userRepo,
        private RoomRepository $roomRepo
    ) {
    }

    public function getUserDashboardData(int $userId): array
    {
        $user = $this->userRepo->findById($userId);

        $bookings = $this->bookingRepo->getUserActiveBookings($userId);
        $pendingFeedbacks = $this->bookingRepo->getUserPendingFeedbacks($userId);

        return [
            'user' => $user,
            'bookings' => $bookings,
            'pendingFeedbacks' => $pendingFeedbacks,
        ];
    }

    public function getAdminDashboardData(): array
    {
        $bookingStats = [
            'total' => $this->bookingRepo->getTotalBookings(),
            'statuses' => $this->bookingRepo->getBookingCountByStatus(),
        ];

        $resourceStats = [
            'rooms' => [
                'total' => $this->roomRepo->getTotalRooms(),
                'available' => $this->roomRepo->getAvailableRooms(),
                'unavailable' => $this->roomRepo->getUnavailableRooms(),
            ],

            'users' => [
                'total' => $this->userRepo->getTotalUsers(),
                'active' => $this->userRepo->getActiveUsers(),
                'pending kubaca' => $this->userRepo->getPendingKubacaUsers(),
                'nonaktif' => $this->userRepo->getNonaktifUsers(),
                'pending verifikasi email' => $this->userRepo->getPendingVerificationEmail(),
                'rejected' => $this->userRepo->getRejectedUsers(),
                'suspended' => $this->userRepo->getSuspendedUsers(),
            ]
        ];

        $recentBookings = $this->bookingRepo->getRecentBookings(10);
        $roomUsage = $this->bookingRepo->getRoomUsageStatistics();

        return [
            'stats' => [
                'bookingStats' => $bookingStats,
                'resources' => $resourceStats,
            ],
            'recentBookings' => $recentBookings,
            'roomUsage' => $roomUsage,
        ];
    }
}