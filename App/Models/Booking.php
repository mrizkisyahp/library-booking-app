<?php

namespace App\Models;

use App\Core\DbModel;
use App\Core\App;

class Booking extends DbModel {
    public ?int $id_booking = null;
    public ?int $user_id = null;
    public ?int $ruangan_id = null;
    public string $tanggal_booking = '';
    public string $tanggal_penggunaan_ruang = '';
    public string $waktu_mulai = '';
    public string $waktu_selesai = '';
    public string $tujuan = '';
    public string $status = 'draft'; // draft, pending, verified, active, completed, cancelled, expired, no_show
    public ?string $checkin_code = null;
    public ?string $invite_token = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function tableName(): string {
        return 'booking';
    }

    public static function primaryKey(): string {
        return 'id_booking';
    }

    public function attributes(): array {
        return [
            'user_id',
            'ruangan_id',
            'tanggal_booking',
            'tanggal_penggunaan_ruang',
            'waktu_mulai',
            'waktu_selesai',
            'tujuan',
            'status',
            'checkin_code',
            'invite_token',
        ];
    }

    public function rules(): array {
        return [];
    }

    public static function expireStaleDrafts(): void
    {
        $stmt = App::$app->db->prepare("
            UPDATE booking
            SET status = 'expired'
            WHERE status = 'draft'
              AND (
                    TIMESTAMPDIFF(HOUR, created_at, NOW()) >= 24
                 OR (tanggal_penggunaan_ruang = CURDATE() AND waktu_mulai <= CURTIME())
              )
        ");
        $stmt->execute();
    }

    public static function userHasPendingFeedback(int $userId): bool
    {
        $stmt = App::$app->db->prepare("
            SELECT COUNT(*) AS cnt
            FROM booking b
            WHERE b.user_id = :user_id
              AND b.status = 'completed'
              AND NOT EXISTS (
                SELECT 1 FROM feedback f WHERE f.booking_id = b.id_booking
              )
        ");
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function getPendingFeedbackBookings(int $userId): array
    {
        $stmt = App::$app->db->prepare("
            SELECT b.id_booking, b.tanggal_penggunaan_ruang, b.waktu_mulai, r.nama_ruangan
            FROM booking b
            JOIN ruangan r ON r.id_ruangan = b.ruangan_id
            WHERE b.user_id = :user_id
              AND b.status = 'completed'
              AND NOT EXISTS (
                SELECT 1 FROM feedback f WHERE f.booking_id = b.id_booking
              )
            ORDER BY b.tanggal_penggunaan_ruang DESC, b.waktu_mulai DESC
        ");
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function userHasUpcomingBooking(int $userId, string $date): bool
    {
        $stmt = App::$app->db->prepare("
            SELECT COUNT(*) FROM booking
            WHERE user_id = :user_id
              AND status IN ('draft','pending','verified','active')
              AND tanggal_penggunaan_ruang >= :date
        ");
        $stmt->bindValue(':user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':date', $date);
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    public function getMembers(): array {
        $stmt = App::$app->db->prepare("
        SELECT ab.*, u.nama, u.email
        FROM anggota_booking ab
        JOIN users u ON u.id_user = ab.user_id
        WHERE ab.booking_id = :id
        ");
        $stmt->bindValue(":id", $this->id_booking, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getMemberCount(): int
    {
        $stmt = App::$app->db->prepare("SELECT COUNT(*) FROM anggota_booking WHERE booking_id = :id");
        $stmt->bindValue(':id', $this->id_booking, \PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getMinimumMembersRequired(): int
    {
        $room = Room::findOne(['id_ruangan' => $this->ruangan_id]);
        return $room && $room->kapasitas_min ? (int)$room->kapasitas_min : 0;
    }

    public function meetsMemberMinimum(): bool
    {
        $required = $this->getMinimumMembersRequired();
        if ($required <= 0) {
            return true;
        }
        return $this->getMemberCount() >= $required;
    }
}
