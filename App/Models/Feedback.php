<?php

namespace App\Models;

use App\Core\DbModel;
use App\Core\App;

class Feedback extends DbModel
{
    public ?int $id_feedback = null;
    public ?int $booking_id = null;
    public ?int $user_id = null;
    public ?string $nama_ruangan = null;
    public ?int $rating = null;
    public string $nama = '';
    public string $tanggal_booking = '';
    public string $tanggal_penggunaan_ruang = '';
    public string $waktu_mulai = '';
    public string $waktu_selesai = '';
    public string $tujuan = '';
    public string $status = 'draft'; // draft, pending, verified, active, completed, cancelled, expired, no_show
    public ?string $checkin_code = null;
    public ?string $invite_token = null;
    public ?string $komentar = '';
    // public ?string $created_at = null;
    // public ?string $updated_at = null;

    public static function tableName(): string
    {
        return 'feedback';
    }

    public static function primaryKey(): string
    {
        return 'id_feedback';
    }

    public function attributes(): array
    {
        return [
            'booking_id',
            'user_id',
            'rating',
            'komentar'
        ];
    }

    public function rules(): array
    {
        return [];
    }

    public static function findPaginated(int $page, int $perPage, array $filters = [])
    {
        $offset = ($page - 1) * $perPage;

        [$baseSql, $params] = self::buildQuery($filters);

        $sql = $baseSql . " ORDER BY feedback.id_feedback DESC LIMIT :limit OFFSET :offset";

        $stmt = App::$app->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);
    }

    private static function buildQuery(array $filters): array
    {
        $sql = "SELECT feedback.id_feedback, feedback.booking_id, feedback.user_id, feedback.rating, feedback.komentar, users.nama, booking.tanggal_booking, booking.tanggal_penggunaan_ruang, booking.waktu_mulai, booking.waktu_selesai, booking.tujuan, booking.status, booking.checkin_code, booking.invite_token, ruangan.nama_ruangan
        FROM feedback
        LEFT JOIN users ON feedback.user_id = users.id_user
        LEFT JOIN booking ON feedback.booking_id = booking.id_booking
        LEFT JOIN ruangan ON booking.ruangan_id = ruangan.id_ruangan";

        $params = [];
        $conditions = [];

        if (!empty($filters['keyword'])) {
            $conditions[] = "(users.nama LIKE :keyword OR ruangan.nama_ruangan LIKE :keyword)";
            $params[':keyword'] = '%' . $filters['keyword'] . '%';
        }

        if (!empty($filters['tanggal_penggunaan_ruang'])) {
            $conditions[] = "booking.tanggal_penggunaan_ruang = :tanggal_penggunaan_ruang";
            $params[':tanggal_penggunaan_ruang'] = $filters['tanggal_penggunaan_ruang'];
        }

        if (!empty($filters['rating'])) {
            $conditions[] = "feedback.rating = :rating";
            $params[':rating'] = $filters['rating'];
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        return [$sql, $params];
    }

    public static function count(array $filters = []): int
    {
        [$baseSql, $params] = self::buildQuery($filters);

        $sql = "SELECT COUNT(*) FROM ($baseSql) AS count";

        $stmt = App::$app->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
}