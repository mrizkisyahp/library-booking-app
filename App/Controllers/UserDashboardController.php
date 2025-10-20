<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\App;
use App\Core\Middleware\AuthMiddleware;

class UserDashboardController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware());
    }

    public function index()
    {
        $user = App::$app->user;

        if ($user->role === 'mahasiswa' && $user->status === 'active' && !$user->kubaca_img) {
            App::$app->session->setFlash('warning', 'Warning! Your account has not been verified, please upload kubaca image.');
        }

        $stats = $this->getUserStatistics($user->id);
        $recentBookings = $this->getRecentBookings($user->id);

        $this->setTitle('Dashboard | Library Booking App');
        $this->setLayout('main');
        return $this->render('user/dashboard', [
            'user' => $user,
            'stats' => $stats,
            'bookings' => $recentBookings
        ]);
    }

    private function getUserStatistics(int $userId): array
    {
        $db = App::$app->db;

        $stmt = $db->prepare("SELECT COUNT(*) as count FROM bookings WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        $totalBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        $stmt = $db->prepare("SELECT COUNT(*) as count FROM bookings WHERE user_id = :user_id AND status = 'pending'");
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        $pendingBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        $stmt = $db->prepare("SELECT COUNT(*) as count FROM bookings WHERE user_id = :user_id AND status = 'validated'");
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        $validatedBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        $stmt = $db->prepare("SELECT COUNT(*) as count FROM bookings WHERE user_id = :user_id AND status = 'active'");
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        $activeBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        $stmt = $db->prepare("SELECT COUNT(*) as count FROM bookings WHERE user_id = :user_id AND status = 'completed'");
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        $completedBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        return [
            'totalBookings' => $totalBookings,
            'pendingBookings' => $pendingBookings,
            'validatedBookings' => $validatedBookings,
            'activeBookings' => $activeBookings,
            'completedBookings' => $completedBookings
        ];
    }

    private function getRecentBookings(int $userId): array
    {
        $sql = "SELECT b.*, r.title as room_title
                FROM bookings b
                INNER JOIN rooms r ON b.room_id = r.id
                WHERE b.user_id = :user_id
                ORDER BY b.created_at DESC
                LIMIT 5";

        $stmt = App::$app->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
