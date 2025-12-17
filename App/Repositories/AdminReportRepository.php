<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class AdminReportRepository
{
    private PDO $db;

    /**
     * Statuses to exclude from reports.
     * Change to empty array [] to include all statuses.
     */
    private const EXCLUDED_STATUSES = ['draft', 'pending'];

    public function __construct(Database $database)
    {
        $this->db = $database->pdo;
    }

    /**
     * Build the status exclusion WHERE clause
     */
    private function getStatusExclusionSql(string $alias = 'b'): string
    {
        if (empty(self::EXCLUDED_STATUSES)) {
            return '';
        }
        $placeholders = implode(',', array_fill(0, count(self::EXCLUDED_STATUSES), '?'));
        return " AND {$alias}.status NOT IN ($placeholders)";
    }

    /**
     * Get the excluded status values for binding
     */
    private function getExcludedStatusParams(): array
    {
        return self::EXCLUDED_STATUSES;
    }

    /**
     * Fetch summary statistics (total, completed, no_show, cancelled)
     */
    public function fetchSummary(array $filters = []): array
    {
        $where = ['b.deleted_at IS NULL'];
        $params = [];

        if (!empty($filters['start_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) >= ?';
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) <= ?';
            $params[] = $filters['end_date'];
        }

        // Add status exclusion
        $statusExclusion = $this->getStatusExclusionSql();
        $statusParams = $this->getExcludedStatusParams();

        $whereSql = 'WHERE ' . implode(' AND ', $where) . $statusExclusion;
        $allParams = array_merge($params, $statusParams);

        // Total
        $stmt = $this->db->prepare("SELECT COUNT(*) AS total FROM booking b $whereSql");
        $stmt->execute($allParams);
        $total = (int) ($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        // Completed
        $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM booking b $whereSql AND b.status = 'completed'");
        $stmt->execute($allParams);
        $completed = (int) ($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0);

        // No Show
        $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM booking b $whereSql AND b.status = 'no_show'");
        $stmt->execute($allParams);
        $noShow = (int) ($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0);

        // Cancelled
        $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM booking b $whereSql AND b.status = 'cancelled'");
        $stmt->execute($allParams);
        $cancelled = (int) ($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0);

        // Active
        $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM booking b $whereSql AND b.status = 'active'");
        $stmt->execute($allParams);
        $active = (int) ($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0);

        // Verified
        $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM booking b $whereSql AND b.status = 'verified'");
        $stmt->execute($allParams);
        $verified = (int) ($stmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0);

        return [
            'total' => $total,
            'completed' => $completed,
            'no_show' => $noShow,
            'cancelled' => $cancelled,
            'active' => $active,
            'verified' => $verified,
        ];
    }

    /**
     * Fetch total bookings grouped by period (day/week/month/semester/year)
     */
    public function fetchTotalByPeriod(array $filters, string $period = 'day'): array
    {
        $where = ['b.deleted_at IS NULL'];
        $params = [];

        if (!empty($filters['start_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) >= ?';
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) <= ?';
            $params[] = $filters['end_date'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'b.status = ?';
            $params[] = $filters['status'];
        }

        $statusExclusion = $this->getStatusExclusionSql();
        $statusParams = $this->getExcludedStatusParams();
        $whereSql = 'WHERE ' . implode(' AND ', $where) . $statusExclusion;
        $allParams = array_merge($params, $statusParams);

        switch ($period) {
            case 'day':
                // Group by day of week (Senin, Selasa, etc.)
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
                break;

            case 'week':
                $sql = "
                    SELECT
                        CONCAT('Minggu ', WEEK(b.tanggal_penggunaan_ruang, 1)) AS label,
                        YEARWEEK(b.tanggal_penggunaan_ruang, 1) AS sort_key,
                        COUNT(*) AS value
                    FROM booking b
                    $whereSql
                    GROUP BY YEARWEEK(b.tanggal_penggunaan_ruang, 1)
                    ORDER BY sort_key ASC
                    LIMIT 52
                ";
                break;

            case 'month':
                $sql = "
                    SELECT
                        DATE_FORMAT(b.tanggal_penggunaan_ruang, '%b %Y') AS label,
                        DATE_FORMAT(b.tanggal_penggunaan_ruang, '%Y-%m') AS sort_key,
                        COUNT(*) AS value
                    FROM booking b
                    $whereSql
                    GROUP BY DATE_FORMAT(b.tanggal_penggunaan_ruang, '%Y-%m')
                    ORDER BY sort_key ASC
                    LIMIT 24
                ";
                break;

            case 'semester':
                // Semester: Jan-Jun = Semester 1, Jul-Dec = Semester 2
                $sql = "
                    SELECT
                        CONCAT('Semester ', IF(MONTH(b.tanggal_penggunaan_ruang) <= 6, 1, 2), ' ', YEAR(b.tanggal_penggunaan_ruang)) AS label,
                        CONCAT(YEAR(b.tanggal_penggunaan_ruang), '-', IF(MONTH(b.tanggal_penggunaan_ruang) <= 6, 1, 2)) AS sort_key,
                        COUNT(*) AS value
                    FROM booking b
                    $whereSql
                    GROUP BY YEAR(b.tanggal_penggunaan_ruang), IF(MONTH(b.tanggal_penggunaan_ruang) <= 6, 1, 2)
                    ORDER BY sort_key ASC
                    LIMIT 10
                ";
                break;

            case 'year':
                $sql = "
                    SELECT
                        YEAR(b.tanggal_penggunaan_ruang) AS label,
                        YEAR(b.tanggal_penggunaan_ruang) AS sort_key,
                        COUNT(*) AS value
                    FROM booking b
                    $whereSql
                    GROUP BY YEAR(b.tanggal_penggunaan_ruang)
                    ORDER BY sort_key ASC
                    LIMIT 10
                ";
                break;

            default:
                // Daily trend (by actual date)
                $sql = "
                    SELECT
                        DATE_FORMAT(b.tanggal_penggunaan_ruang, '%d %b') AS label,
                        DATE(b.tanggal_penggunaan_ruang) AS sort_key,
                        COUNT(*) AS value
                    FROM booking b
                    $whereSql
                    GROUP BY DATE(b.tanggal_penggunaan_ruang)
                    ORDER BY sort_key ASC
                    LIMIT 60
                ";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($allParams);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_column($rows, 'label'),
            'values' => array_map('intval', array_column($rows, 'value')),
        ];
    }

    /**
     * Fetch favorite rooms (most booked)
     */
    public function fetchFavoriteRooms(array $filters, int $limit = 10): array
    {
        $where = ['b.deleted_at IS NULL'];
        $params = [];

        if (!empty($filters['start_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) >= ?';
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) <= ?';
            $params[] = $filters['end_date'];
        }

        // Add status filter if provided
        if (!empty($filters['status'])) {
            $where[] = 'b.status = ?';
            $params[] = $filters['status'];
        }

        $statusExclusion = $this->getStatusExclusionSql();
        $statusParams = $this->getExcludedStatusParams();
        $whereSql = 'WHERE ' . implode(' AND ', $where) . $statusExclusion;
        $allParams = array_merge($params, $statusParams);
        $limitInt = (int) $limit;

        $sql = "
            SELECT r.nama_ruangan AS label, COUNT(b.id_booking) AS value
            FROM booking b
            JOIN ruangan r ON r.id_ruangan = b.ruangan_id
            $whereSql
            GROUP BY r.id_ruangan, r.nama_ruangan
            ORDER BY value DESC
            LIMIT $limitInt
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($allParams);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_column($rows, 'label'),
            'values' => array_map('intval', array_column($rows, 'value')),
        ];
    }

    /**
     * Fetch bookings by department (jurusan)
     */
    public function fetchByDepartment(array $filters): array
    {
        $where = ['b.deleted_at IS NULL'];
        $params = [];

        if (!empty($filters['start_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) >= ?';
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) <= ?';
            $params[] = $filters['end_date'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'b.status = ?';
            $params[] = $filters['status'];
        }

        $statusExclusion = $this->getStatusExclusionSql();
        $statusParams = $this->getExcludedStatusParams();
        $whereSql = 'WHERE ' . implode(' AND ', $where) . $statusExclusion;
        $allParams = array_merge($params, $statusParams);

        $sql = "
            SELECT 
                COALESCE(NULLIF(u.jurusan, ''), 'Tidak Diketahui') AS label,
                COUNT(b.id_booking) AS value
            FROM booking b
            LEFT JOIN users u ON b.user_id = u.id_user
            $whereSql
            GROUP BY u.jurusan
            ORDER BY value DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($allParams);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_column($rows, 'label'),
            'values' => array_map('intval', array_column($rows, 'value')),
        ];
    }

    /**
     * Fetch bookings by purpose (tujuan)
     */
    public function fetchByPurpose(array $filters, int $limit = 10): array
    {
        $where = ['b.deleted_at IS NULL'];
        $params = [];

        if (!empty($filters['start_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) >= ?';
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) <= ?';
            $params[] = $filters['end_date'];
        }

        // Add status filter if provided
        if (!empty($filters['status'])) {
            $where[] = 'b.status = ?';
            $params[] = $filters['status'];
        }

        $statusExclusion = $this->getStatusExclusionSql();
        $statusParams = $this->getExcludedStatusParams();
        $whereSql = 'WHERE ' . implode(' AND ', $where) . $statusExclusion;
        $allParams = array_merge($params, $statusParams);
        $limitInt = (int) $limit;

        $sql = "
            SELECT 
                COALESCE(NULLIF(b.tujuan, ''), 'Tidak Disebutkan') AS label,
                COUNT(*) AS value
            FROM booking b
            $whereSql
            GROUP BY b.tujuan
            ORDER BY value DESC
            LIMIT $limitInt
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($allParams);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_column($rows, 'label'),
            'values' => array_map('intval', array_column($rows, 'value')),
        ];
    }

    /**
     * Fetch busy hours distribution
     */
    public function fetchBusyHours(array $filters): array
    {
        $where = ['b.deleted_at IS NULL'];
        $params = [];

        if (!empty($filters['start_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) >= ?';
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) <= ?';
            $params[] = $filters['end_date'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'b.status = ?';
            $params[] = $filters['status'];
        }

        $statusExclusion = $this->getStatusExclusionSql();
        $statusParams = $this->getExcludedStatusParams();
        $whereSql = 'WHERE ' . implode(' AND ', $where) . $statusExclusion;
        $allParams = array_merge($params, $statusParams);

        $sql = "
            SELECT 
                HOUR(b.waktu_mulai) AS hour,
                CONCAT(LPAD(HOUR(b.waktu_mulai), 2, '0'), ':00') AS label,
                COUNT(*) AS value
            FROM booking b
            $whereSql
            GROUP BY HOUR(b.waktu_mulai)
            ORDER BY hour ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($allParams);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'labels' => array_column($rows, 'label'),
            'values' => array_map('intval', array_column($rows, 'value')),
        ];
    }

    /**
     * Fetch detailed report rows for table/export
     */
    public function fetchReportRows(array $filters, int $limit = 500): array
    {
        $where = ['b.deleted_at IS NULL'];
        $params = [];

        if (!empty($filters['start_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) >= ?';
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $where[] = 'DATE(b.tanggal_penggunaan_ruang) <= ?';
            $params[] = $filters['end_date'];
        }
        if (!empty($filters['status'])) {
            $where[] = 'b.status = ?';
            $params[] = $filters['status'];
        }

        $statusExclusion = $this->getStatusExclusionSql();
        $statusParams = $this->getExcludedStatusParams();
        $whereSql = 'WHERE ' . implode(' AND ', $where) . $statusExclusion;
        $allParams = array_merge($params, $statusParams);

        // Cast limit to int for safety since we can't use placeholder for LIMIT in MySQL
        $limitInt = (int) $limit;

        $sql = "
            SELECT
                b.id_booking,
                b.checkin_code AS kode_booking,
                u.nama AS user_name,
                u.jurusan AS user_jurusan,
                r.nama_ruangan AS room_name,
                b.tujuan,
                b.status,
                DATE_FORMAT(b.tanggal_penggunaan_ruang, '%d/%m/%Y') AS tanggal,
                TIME_FORMAT(b.waktu_mulai, '%H:%i') AS waktu_mulai,
                TIME_FORMAT(b.waktu_selesai, '%H:%i') AS waktu_selesai
            FROM booking b
            LEFT JOIN users u ON b.user_id = u.id_user
            LEFT JOIN ruangan r ON b.ruangan_id = r.id_ruangan
            $whereSql
            ORDER BY b.tanggal_penggunaan_ruang DESC, b.waktu_mulai ASC
            LIMIT $limitInt
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($allParams);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
