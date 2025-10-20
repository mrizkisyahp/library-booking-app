<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Middleware\AdminMiddleware;
use App\Core\App;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware());
    }

    public function index()
    {
        $this->setTitle('Admin Dashboard | Library Booking App');
        $this->setLayout('main');

        $stats = $this->getStatistics();
        $recentBookings = $this->getRecentBookings();
        $roomUsage = $this->getRoomUsage();

        return $this->render('admin/dashboard', [
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'roomUsage' => $roomUsage
        ]);
    }

    // statistik 
    private function getStatistics(): array
    {
        $db = App::$app->db;

        // Total bookings
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM bookings");
        $stmt->execute();
        $totalBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Pending bookings
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'");
        $stmt->execute();
        $pendingBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Active bookings
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM bookings WHERE status = 'active'");
        $stmt->execute();
        $activeBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Completed bookings
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM bookings WHERE status = 'completed'");
        $stmt->execute();
        $completedBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Total rooms
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM rooms");
        $stmt->execute();
        $totalRooms = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Available rooms
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM rooms WHERE status = 'available'");
        $stmt->execute();
        $availableRooms = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Total users
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE role != 'admin'");
        $stmt->execute();
        $totalUsers = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Verified users
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users WHERE status = 'verified' AND role != 'admin'");
        $stmt->execute();
        $verifiedUsers = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        return [
            'totalBookings' => $totalBookings,
            'pendingBookings' => $pendingBookings,
            'activeBookings' => $activeBookings,
            'completedBookings' => $completedBookings,
            'totalRooms' => $totalRooms,
            'availableRooms' => $availableRooms,
            'totalUsers' => $totalUsers,
            'verifiedUsers' => $verifiedUsers
        ];
    }

    // recent booking
    private function getRecentBookings(): array
    {
        $sql = "SELECT b.*, u.nama as user_name, r.title as room_title
                FROM bookings b
                INNER JOIN users u ON b.user_id = u.id
                INNER JOIN rooms r ON b.room_id = r.id
                ORDER BY b.created_at DESC
                LIMIT 10";

        $stmt = App::$app->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // room usage
    private function getRoomUsage(): array
    {
        $sql = "SELECT r.title, COUNT(b.id) as booking_count
                FROM rooms r
                LEFT JOIN bookings b ON r.id = b.room_id
                GROUP BY r.id, r.title
                ORDER BY booking_count DESC";

        $stmt = App::$app->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
