<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Middleware\AuthMiddleware;
use App\Models\Role;
use App\Models\Booking;
use App\Models\User;

class UserDashboardController extends Controller
{
    protected ?User $currentUser = null;
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware());
        $this->currentUser = App::$app->user instanceof User ? App::$app->user : null;
    }

    public function index()
    {
        Booking::expireStaleDrafts();
        $user = $this->currentUser;

        if ((int)$user->id_role === 1) {
            App::$app->response->redirect('/admin');
            return;
        }

        $roleName = Role::getNameById($user->id_role ?? null);
        if ($roleName === 'mahasiswa' && $user->status === 'pending kubaca' && !$user->kubaca_img) {
            App::$app->session->setFlash('warning', 'Warning! Your account has not been verified fully, please upload kubaca image.');
        } else if ($roleName === 'mahasiswa' && $user->status === 'rejected') {
            App::$app->session->setFlash('warning', 'Warning! Your kubaca image has been rejected, please reupload kubaca in profile.');
        }

        $userId = (int)$user->id_user;
        $stats = $this->getUserStatistics($userId);
        $bookings = App::$app->db->pdo->prepare("
            SELECT b.*, r.nama_ruangan,
            'PIC' AS role,
                EXISTS(SELECT 1 FROM feedback f WHERE f.booking_id = b.id_booking) AS feedback_submitted
            FROM booking b
            JOIN ruangan r ON r.id_ruangan = b.ruangan_id
            WHERE b.user_id = :user

            UNION ALL

            SELECT b.*, r.nama_ruangan,
                'Anggota' AS role,
                EXISTS(SELECT 1 FROM feedback f WHERE f.booking_id = b.id_booking) AS feedback_submitted
            FROM booking b
            JOIN ruangan r ON r.id_ruangan = b.ruangan_id
            JOIN anggota_booking ab ON ab.booking_id = b.id_booking
            WHERE ab.user_id = :user AND b.user_id <> :user
            ORDER BY created_at DESC
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

        $statusList = ['draft', 'pending', 'pending', 'active', 'completed', 'cancelled', 'expired', 'no_show'];
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
