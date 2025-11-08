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

        return $this->render('Admin/AdminDashboard', [
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
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM booking");
        $stmt->execute();
        $totalBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Pending bookings
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM booking WHERE status = 'pending'");
        $stmt->execute();
        $pendingBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Ongoing bookings
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM booking WHERE status = 'active'");
        $stmt->execute();
        $activeBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Completed bookings
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM booking WHERE status = 'completed'");
        $stmt->execute();
        $completedBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Total rooms
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM ruangan");
        $stmt->execute();
        $totalRooms = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Available rooms
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM ruangan WHERE status_ruangan = 'available'");
        $stmt->execute();
        $availableRooms = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Total users
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users u INNER JOIN role r ON u.id_role = r.id_role WHERE r.nama_role != 'admin'");
        $stmt->execute();
        $totalUsers = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Verified users
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users u INNER JOIN role r ON u.id_role = r.id_role WHERE u.status = 'verified' AND r.nama_role != 'admin'");
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
        $sql = "SELECT b.*, u.nama as user_name, r.nama_ruangan as room_title
                FROM booking b
                INNER JOIN users u ON b.user_id = u.id_user
                INNER JOIN ruangan r ON b.ruangan_id = r.id_ruangan
                ORDER BY b.created_at DESC
                LIMIT 10";

        $stmt = App::$app->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // room usage
    private function getRoomUsage(): array
    {
        $sql = "SELECT r.nama_ruangan, COUNT(b.id_booking) as booking_count
                FROM ruangan r
                LEFT JOIN booking b ON r.id_ruangan = b.ruangan_id
                GROUP BY r.id_ruangan, r.nama_ruangan
                ORDER BY booking_count DESC";

        $stmt = App::$app->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
