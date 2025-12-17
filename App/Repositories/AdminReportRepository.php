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

    /**
     * Fetch bookings grouped by user jurusan (department)
     */
    public function fetchBookingsByDepartment(array $filters): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['start_date'])) {
            $where[] = "DATE(b.tanggal_penggunaan_ruang) >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = "DATE(b.tanggal_penggunaan_ruang) <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        $whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "
            SELECT 
                COALESCE(u.jurusan, 'Tidak Diketahui') AS label, 
                COUNT(b.id_booking) AS value
            FROM booking b
            LEFT JOIN users u ON b.user_id = u.id_user
            $whereSQL
            GROUP BY u.jurusan
            ORDER BY value DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_column($rows, 'label'),
            'values' => array_column($rows, 'value'),
        ];
    }

    /**
     * Fetch bookings grouped by reason/purpose (tujuan)
     */
    public function fetchBookingsByReason(array $filters): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['start_date'])) {
            $where[] = "DATE(b.tanggal_penggunaan_ruang) >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = "DATE(b.tanggal_penggunaan_ruang) <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        $whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

        // Group by first 50 chars of tujuan to avoid too many categories
        $sql = "
            SELECT 
                SUBSTRING(b.tujuan, 1, 50) AS label, 
                COUNT(*) AS value
            FROM booking b
            $whereSQL
            GROUP BY SUBSTRING(b.tujuan, 1, 50)
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

    // Fetch bookings grouped by day of week (Senin, Selasa, etc.)
    public function fetchBookingsByDay(array $filters): array
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
                DAYOFWEEK(b.tanggal_penggunaan_ruang) AS day_num,
                CASE DAYOFWEEK(b.tanggal_penggunaan_ruang)
                    WHEN 1 THEN 'Minggu'
                    WHEN 2 THEN 'Senin'
                    WHEN 3 THEN 'Selasa'
                    WHEN 4 THEN 'Rabu'
                    WHEN 5 THEN 'Kamis'
                    WHEN 6 THEN 'Jumat'
                    WHEN 7 THEN 'Sabtu'
                END AS label,
                COUNT(*) AS value
            FROM booking b
            $whereSql
            GROUP BY DAYOFWEEK(b.tanggal_penggunaan_ruang)
            ORDER BY day_num ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_column($rows, 'label'),
            'values' => array_column($rows, 'value'),
        ];
    }

    // Fetch bookings grouped by week
    public function fetchBookingsByWeek(array $filters): array
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
                CONCAT('Minggu ', WEEK(b.tanggal_penggunaan_ruang, 1), ' (', 
                    DATE_FORMAT(DATE_SUB(b.tanggal_penggunaan_ruang, INTERVAL WEEKDAY(b.tanggal_penggunaan_ruang) DAY), '%d/%m'),
                    ')') AS label,
                YEARWEEK(b.tanggal_penggunaan_ruang, 1) AS week_num,
                COUNT(*) AS value
            FROM booking b
            $whereSql
            GROUP BY YEARWEEK(b.tanggal_penggunaan_ruang, 1)
            ORDER BY week_num ASC
            LIMIT 12
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_column($rows, 'label'),
            'values' => array_column($rows, 'value'),
        ];
    }

    // Fetch bookings grouped by month
    public function fetchBookingsByMonth(array $filters): array
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
                DATE_FORMAT(b.tanggal_penggunaan_ruang, '%b %Y') AS label,
                DATE_FORMAT(b.tanggal_penggunaan_ruang, '%Y-%m') AS month_num,
                COUNT(*) AS value
            FROM booking b
            $whereSql
            GROUP BY DATE_FORMAT(b.tanggal_penggunaan_ruang, '%Y-%m')
            ORDER BY month_num ASC
            LIMIT 12
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_column($rows, 'label'),
            'values' => array_column($rows, 'value'),
        ];
    }
}
