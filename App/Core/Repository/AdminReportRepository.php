<?php

namespace App\Core\Repository;

use App\Core\App;
use PDO;

class AdminReportRepository
{
    private PDO $conn;

    public function __construct()
    {
        // Ambil koneksi PDO murni, bukan wrapper
        $this->conn = App::$app->db->pdo;
    }

    /**
     * Query summary total booking per status
     */
    public function fetchSummary(array $filters): array
    {
        $sql = "SELECT
                    COUNT(*) AS total,
                    SUM(CASE WHEN status = 'completed' THEN 1 END) AS completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 END) AS cancelled
                FROM booking
                WHERE 1=1";

        $params = [];

        if (!empty($filters['start_date'])) {
            $sql .= " AND tanggal_penggunaan_ruang >= :start";
            $params[':start'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND tanggal_penggunaan_ruang <= :end";
            $params[':end'] = $filters['end_date'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Query data untuk chart
     * (Jumlah booking per tanggal)
     */
    public function fetchChartData(array $filters): array
    {
        $sql = "SELECT
                    tanggal_penggunaan_ruang AS date,
                    COUNT(*) AS total
                FROM booking
                WHERE 1=1";

        $params = [];

        if (!empty($filters['start_date'])) {
            $sql .= " AND tanggal_penggunaan_ruang >= :start";
            $params[':start'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND tanggal_penggunaan_ruang <= :end";
            $params[':end'] = $filters['end_date'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        $sql .= " GROUP BY tanggal_penggunaan_ruang ORDER BY tanggal_penggunaan_ruang ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Query tabel detail booking
     */
    public function fetchTableData(array $filters): array
    {
        $sql = "SELECT
                    b.id_booking,
                    u.nama AS user_name,
                    r.nama_ruangan AS room_name,
                    b.status,
                    b.tanggal_penggunaan_ruang
                FROM booking b
                INNER JOIN users u ON b.user_id = u.id_user
                INNER JOIN ruangan r ON b.ruangan_id = r.id_ruangan
                WHERE 1=1";

        $params = [];

        if (!empty($filters['start_date'])) {
            $sql .= " AND b.tanggal_penggunaan_ruang >= :start";
            $params[':start'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= " AND b.tanggal_penggunaan_ruang <= :end";
            $params[':end'] = $filters['end_date'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND b.status = :status";
            $params[':status'] = $filters['status'];
        }

        $sql .= " ORDER BY b.tanggal_penggunaan_ruang DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
