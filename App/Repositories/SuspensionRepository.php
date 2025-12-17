<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class SuspensionRepository
{
    public function __construct(private Database $db)
    {
    }

    /**
     * Create suspension record
     */
    public function create(int $userId, string $date): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO suspensi (id_akun, tgl_suspensi, created_at)
            VALUES (:id_akun, :tgl_suspensi, :created_at)
        ");
        $stmt->execute([
            ':id_akun' => $userId,
            ':tgl_suspensi' => $date,
            ':created_at' => date('Y-m-d H:i:s')
        ]);

        return (int) $this->db->pdo->lastInsertId();
    }

    /**
     * Get all suspensions PAGINATED
     */
    public function getAllPaginated(int $perPage = 15, int $page = 1): \App\Core\Paginator
    {
        $offset = ($page - 1) * $perPage;

        // Get total count
        $countStmt = $this->db->pdo->query("
            SELECT COUNT(*) FROM suspensi WHERE deleted_at IS NULL
        ");
        $total = (int) $countStmt->fetchColumn();

        // Get items
        $stmt = $this->db->prepare("
            SELECT s.*, u.nama, u.email, u.status as user_status
            FROM suspensi s
            LEFT JOIN users u ON s.id_akun = u.id_user
            WHERE s.deleted_at IS NULL
            ORDER BY s.tgl_suspensi DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $lastPage = (int) ceil($total / $perPage);

        return new \App\Core\Paginator($items, $total, $perPage, $page, $lastPage);
    }

    /**
     * Get all suspensions with pagination - legacy
     */
    public function getAll(int $limit = 100, int $offset = 0): array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, u.nama, u.email, u.status as user_status
            FROM suspensi s
            LEFT JOIN users u ON s.id_akun = u.id_user
            WHERE s.deleted_at IS NULL
            ORDER BY s.tgl_suspensi DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get suspensions by user
     */
    public function getByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM suspensi 
            WHERE id_akun = :user_id 
            AND deleted_at IS NULL
            ORDER BY tgl_suspensi DESC
        ");
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
