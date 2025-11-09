<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Middleware\AuthMiddleware;
use App\Models\Role;
use App\Models\Booking;

class UserDashboardController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware());
    }

    public function index()
    {
        Booking::expireStaleDrafts();
        /** @var \App\Models\User $user */
        $user = App::$app->user;

        $roleName = Role::getNameById($user->id_role ?? null);
        if ($roleName === 'mahasiswa' && $user->status === 'verified' && !$user->kubaca_img) {
            App::$app->session->setFlash('warning', 'Warning! Your account has not been verified, please upload kubaca image.');
        }

        $userId = (int)$user->id_user;
        $stats = $this->getUserStatistics($userId);
        $bookings = App::$app->db->pdo->prepare("
            SELECT b.*, r.nama_ruangan,
                EXISTS(SELECT 1 FROM feedback f WHERE f.booking_id = b.id_booking) AS feedback_submitted
            FROM booking b
            JOIN ruangan r ON r.id_ruangan = b.ruangan_id
            WHERE b.user_id = :user
            ORDER BY b.created_at DESC
        ");
        $bookings->bindValue(':user', $user->id_user, \PDO::PARAM_INT);
        $bookings->execute();
        $pendingFeedbacks = Booking::getPendingFeedbackBookings($userId);

        return $this->render('User/UserDashboard', [
            'user' => $user,
            'stats' => $stats,
            'bookings' => $bookings->fetchAll(\PDO::FETCH_ASSOC),
            'pendingFeedbacks' => $pendingFeedbacks,
        ]);
    }

    private function getUserStatistics(int $userId): array
    {
        $db = App::$app->db;

        $stmt = $db->prepare("SELECT COUNT(*) as count FROM booking WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        $totalBookings = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        $statusList = ['draft', 'pending', 'verified', 'active', 'completed', 'cancelled', 'expired', 'no_show'];
        $statusCounts = [];
        foreach ($statusList as $status) {
            $statusCounts[$status] = $this->countByStatus($userId, $status);
        }

        return [
            'totalBookings' => $totalBookings,
            'statusCounts' => $statusCounts,
        ];
    }

    private function countByStatus(int $userId, string $status): int
    {
        $stmt = App::$app->db->prepare("
            SELECT COUNT(*) as count FROM booking WHERE user_id = :user_id AND status = :status
        ");
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':status', $status);
        $stmt->execute();
        return (int)$stmt->fetch(\PDO::FETCH_ASSOC)['count'];
    }
}
