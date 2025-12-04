<?php

namespace App\Core\Services;

use App\Core\App;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Models\Role;
use App\Core\Services\FeedbackService;

class UserDashboardService
{
    // public function expireStaleDrafts(): void
    // {
    //     Booking::expireStaleDrafts();
    // }

    public function getBookingStatistics(int $userId): array
    {
        $db = App::$app->db;
        $stmt = $db->prepare('SELECT COUNT(*) AS count FROM booking WHERE user_id = :user_id');
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        $totalBookings = (int) ($stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0);

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
        $stmt = App::$app->db->prepare('SELECT COUNT(*) AS count FROM booking WHERE user_id = :user_id AND status = :status');
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':status', $status);
        $stmt->execute();
        return (int) ($stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0);
    }

    public function getPicBookings(int $userId, int $limit = 1): array
    {
        $sql = "
            SELECT b.*, r.nama_ruangan,
                'PIC' AS role,
                EXISTS(SELECT 1 FROM feedback f WHERE f.booking_id = b.id_booking) AS feedback_submitted
            FROM booking b
            JOIN ruangan r ON r.id_ruangan = b.ruangan_id
            WHERE b.user_id = :user
            ORDER BY b.created_at DESC
            LIMIT :limit
        ";

        $stmt = App::$app->db->prepare($sql);
        $stmt->bindValue(':user', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAnggotaBookings(int $userId, int $limit = 1): array
    {
        $sql = "
            SELECT b.*, r.nama_ruangan,
                'Anggota' AS role,
                EXISTS(SELECT 1 FROM feedback f WHERE f.booking_id = b.id_booking) AS feedback_submitted
            FROM booking b
            JOIN ruangan r ON r.id_ruangan = b.ruangan_id
            JOIN anggota_booking ab ON ab.booking_id = b.id_booking
            WHERE ab.user_id = :user AND b.user_id <> :user
            ORDER BY b.created_at DESC
            LIMIT :limit
        ";

        $stmt = App::$app->db->prepare($sql);
        $stmt->bindValue(':user', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPendingFeedbacks(int $userId): array
    {
        $service = new FeedbackService();
        return $service->getPendingFeedbackBookings($userId);
    }

    public function computeWarnings(User $user): array
    {
        $warnings = [];
        $roleName = Role::getNameById($user->id_role ?? null);

        if ($roleName === 'mahasiswa' && $user->status === 'pending kubaca' && !$user->kubaca_img) {
            $warnings[] = 'Warning! Your account has not been verified fully, please upload KuBaca image.';
        } elseif ($roleName === 'mahasiswa' && $user->status === 'rejected') {
            $warnings[] = 'Warning! Your KuBaca image has been rejected, please reupload KuBaca in profile.';
        }

        return $warnings;
    }

    //     public function getMinimumMembersRequired(?Booking $booking): int
//     {
//         $room = Room::findOne(['id_ruangan' => $booking->ruangan_id]);
//         return $room && $room->kapasitas_min ? (int) $room->kapasitas_min : 0;
//     }

    //     public function getMaximumMembersRequired(?Booking $booking): int
//     {
//         $room = Room::findOne(['id_ruangan' => $booking->ruangan_id]);
//         return $room && $room->kapasitas_max ? (int) $room->kapasitas_max : 0;
//     }

    //     public function getMemberCount(?Booking $booking): int
//     {
//         $db = App::$app->db;

    //         $membersStmt = $db->prepare("
//             SELECT COUNT(*) FROM anggota_booking WHERE booking_id = :id
//         ");
//         $membersStmt->bindValue(':id', $booking->id_booking, \PDO::PARAM_INT);
//         $membersStmt->execute();
//         $members = (int) $membersStmt->fetchColumn();

    //         $picStmt = $db->prepare("
//             SELECT 1 FROM anggota_booking WHERE booking_id = :id AND user_id = :pic LIMIT 1
//         ");
//         $picStmt->bindValue(':id', $booking->id_booking, \PDO::PARAM_INT);
//         $picStmt->bindValue(':pic', $booking->user_id, \PDO::PARAM_INT);
//         $picStmt->execute();
//         $picAlreadyCounted = (bool) $picStmt->fetchColumn();

    //         return $picAlreadyCounted ? $members : $members + 1;
//     }

    // public function meetsMemberRequirement(?Booking $booking): bool
//     {
//         $minRequired = $this->getMinimumMembersRequired($booking) ?? null;
//         $maxRequired = $this->getMaximumMembersRequired($booking) ?? null;
//         $currentCount = $this->getMemberCount($booking) ?? null;

    //         if ($minRequired <= 0 && $maxRequired <= 0) {
//             return true;
//         }

    //         $meetsMin = $minRequired <= 0 || $currentCount >= $minRequired;
//         $meetsMax = $maxRequired <= 0 || $currentCount <= $maxRequired;

    //         return $meetsMin && $meetsMax;
//     }
}
