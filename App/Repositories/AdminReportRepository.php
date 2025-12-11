<?php
namespace App\Repositories;

use App\Core\Database;
use PDO;

class AdminReportRepository
{
    /** @var PDO */
    private $db;

    public function __construct(Database $database)
    {
        $this->db = $database->pdo;
    }

    // Returns associative array with total, completed, cancelled counts
    public function fetchSummary(array $filters = []): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'b.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['start_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) >= :start_date';
            $params[':start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) <= :end_date';
            $params[':end_date'] = $filters['end_date'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // total
        $sqlTotal = "SELECT COUNT(*) AS total FROM booking b $whereSql";
        $stmt = $this->db->prepare($sqlTotal);
        $stmt->execute($params);
        $total = (int) ($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        // completed
        $sqlCompleted = "SELECT COUNT(*) AS completed FROM booking b " . ($where ? $whereSql . " AND b.status = 'completed'" : "WHERE b.status = 'completed'");
        $stmt = $this->db->prepare($sqlCompleted);
        $stmt->execute($params);
        $completed = (int) ($stmt->fetch(PDO::FETCH_ASSOC)['completed'] ?? 0);

        // cancelled
        $sqlCancelled = "SELECT COUNT(*) AS cancelled FROM booking b " . ($where ? $whereSql . " AND b.status = 'cancelled'" : "WHERE b.status = 'cancelled'");
        $stmt = $this->db->prepare($sqlCancelled);
        $stmt->execute($params);
        $cancelled = (int) ($stmt->fetch(PDO::FETCH_ASSOC)['cancelled'] ?? 0);

        return ['total' => $total, 'completed' => $completed, 'cancelled' => $cancelled];
    }

    //* Returns rows grouped by date: tanggal (YYYY-MM-DD) and count
    public function fetchBookingCountsByDate(array $filters = []): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'b.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['start_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) >= :start_date';
            $params[':start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) <= :end_date';
            $params[':end_date'] = $filters['end_date'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT
                DATE(b.tanggal_penggunaan_ruang) AS tanggal,
                COUNT(*) AS count
            FROM booking b
            $whereSql
            GROUP BY DATE(b.tanggal_penggunaan_ruang)
            ORDER BY DATE(b.tanggal_penggunaan_ruang) ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchTopRooms(array $filters): array
    {
        $where = [];
        $params = [];

        // filter tanggal
        if ($filters['start_date']) {
            $where[] = "DATE(b.tanggal_penggunaan_ruang) >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        if ($filters['end_date']) {
            $where[] = "DATE(b.tanggal_penggunaan_ruang) <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        $whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "
            SELECT r.nama_ruangan AS label, COUNT(b.id_booking) AS value
            FROM booking b
            JOIN ruangan r ON r.id_ruangan = b.ruangan_id
            $whereSQL
            GROUP BY r.id_ruangan
            ORDER BY value DESC
            LIMIT 10
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_column($rows, 'label'),
            'values' => array_column($rows, 'value')
        ];
    }

    public function fetchTopFeedback(array $filters): array
    {
        $where = [];
        $params = [];

        if ($filters['start_date']) {
            $where[] = "DATE(b.tanggal_penggunaan_ruang) >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        if ($filters['end_date']) {
            $where[] = "DATE(b.tanggal_penggunaan_ruang) <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        $whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "
            SELECT r.nama_ruangan AS label, COUNT(f.id_feedback) AS value
            FROM feedback f
            JOIN booking b ON b.id_booking = f.booking_id
            JOIN ruangan r ON r.id_ruangan = b.ruangan_id
            $whereSQL
            GROUP BY r.id_ruangan
            ORDER BY value DESC
            LIMIT 10
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_column($rows, 'label'),
            'values' => array_column($rows, 'value'),
        ];
    }

    public function fetchBusyHours(array $filters): array
    {
        $where = [];
        $params = [];

        if ($filters['start_date']) {
            $where[] = "DATE(b.tanggal_penggunaan_ruang) >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        if ($filters['end_date']) {
            $where[] = "DATE(b.tanggal_penggunaan_ruang) <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        $whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "
            SELECT HOUR(b.waktu_mulai) AS hour, COUNT(*) AS value
            FROM booking b
            $whereSQL
            GROUP BY HOUR(b.waktu_mulai)
            ORDER BY hour ASC;
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_map(fn($r) => $r['hour'] . ':00', $rows),
            'values' => array_column($rows, 'value')
        ];
    }




    /**
     * Fetch detailed report rows (code, user, room, status, tanggal)
     */
    public function fetchReportRows(array $filters = []): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'b.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['start_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) >= :start_date';
            $params[':start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) <= :end_date';
            $params[':end_date'] = $filters['end_date'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // kode_booking: create a readable code if actual column doesn't exist
        $sql = "
            SELECT
                CONCAT('BKG', b.id_booking) AS kode_booking,
                u.nama AS user_name,
                r.nama_ruangan AS room_name,
                b.status,
                DATE(b.tanggal_penggunaan_ruang) AS tanggal
            FROM booking b
            LEFT JOIN users u ON b.user_id = u.id_user
            LEFT JOIN ruangan r ON b.ruangan_id = r.id_ruangan
            $whereSql
            ORDER BY b.tanggal_penggunaan_ruang DESC, b.created_at DESC
            LIMIT 1000
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
