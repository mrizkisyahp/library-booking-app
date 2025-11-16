<?php

namespace App\Core\Services;

use App\Core\App;

class AdminDashboardService
{
    public function getGlobalStatistics(): array
    {
        $db = App::$app->db;

        $stmt = $db->prepare('SELECT COUNT(*) AS count FROM booking');
        $stmt->execute();
        $totalBookings = (int)($stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0);

        $statusList = ['draft', 'pending', 'verified', 'active', 'completed', 'cancelled', 'expired', 'no_show'];
        $statusCounts = [];
        foreach ($statusList as $status) {
            $statusCounts[$status] = $this->countBookingByStatus($status);
        }

        $stmt = $db->prepare('SELECT COUNT(*) AS count FROM ruangan');
        $stmt->execute();
        $totalRooms = (int)($stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0);

        $stmt = $db->prepare("SELECT COUNT(*) AS count FROM ruangan WHERE status_ruangan = 'available'");
        $stmt->execute();
        $availableRooms = (int)($stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0);
        $unavailableRooms = max($totalRooms - $availableRooms, 0);

        $stmt = $db->prepare("SELECT COUNT(*) AS count FROM users u INNER JOIN role r ON u.id_role = r.id_role WHERE r.nama_role != 'admin'");
        $stmt->execute();
        $totalUsers = (int)($stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0);

        $stmt = $db->prepare("SELECT COUNT(*) AS count FROM users u INNER JOIN role r ON u.id_role = r.id_role WHERE u.status = 'pending kubaca' AND r.nama_role != 'admin'");
        $stmt->execute();
        $pendingUsers = (int)($stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0);
        $verifiedUsers = max($totalUsers - $pendingUsers, 0);

        return [
            'bookingStats' => [
                'total' => $totalBookings,
                'statuses' => $statusCounts,
            ],
            'resources' => [
                'rooms' => [
                    'total' => $totalRooms,
                    'available' => $availableRooms,
                    'unavailable' => $unavailableRooms,
                ],
                'users' => [
                    'total' => $totalUsers,
                    'verified' => $verifiedUsers,
                    'pending' => $pendingUsers,
                ],
            ],
        ];
    }

    private function countBookingByStatus(string $status): int
    {
        $stmt = App::$app->db->prepare('SELECT COUNT(*) AS count FROM booking WHERE status = :status');
        $stmt->bindValue(':status', $status);
        $stmt->execute();
        return (int)($stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0);
    }

    public function getRecentBookings(int $limit = 10): array
    {
        $sql = "SELECT
                    b.*,
                    u.nama AS user_name,
                    r.nama_ruangan AS room_title,
                    (
                        SELECT id_feedback
                        FROM feedback
                        WHERE booking_id = b.id_booking
                        ORDER BY created_at DESC
                        LIMIT 1
                    ) AS feedback_id
                FROM booking b
                INNER JOIN users u ON b.user_id = u.id_user
                INNER JOIN ruangan r ON b.ruangan_id = r.id_ruangan
                ORDER BY b.created_at DESC
                LIMIT :limit";

        $stmt = App::$app->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getRoomUsage(): array
    {
        $sql = "SELECT r.nama_ruangan, COUNT(b.id_booking) AS booking_count
                FROM ruangan r
                LEFT JOIN booking b ON r.id_ruangan = b.ruangan_id
                GROUP BY r.id_ruangan, r.nama_ruangan
                ORDER BY booking_count DESC";

        $stmt = App::$app->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
