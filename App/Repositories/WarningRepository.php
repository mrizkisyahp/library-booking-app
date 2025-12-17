<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class WarningRepository
{
    public function __construct(private Database $db)
    {
    }

    /**
     * Add warning to user
     */
    public function create(int $userId, int $peringatanId, string $date): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO peringatan_mhs (id_peringatan, id_akun, tgl_peringatan, created_at)
            VALUES (:id_peringatan, :id_akun, :tgl_peringatan, :created_at)
        ");
        $stmt->execute([
            ':id_peringatan' => $peringatanId,
            ':id_akun' => $userId,
            ':tgl_peringatan' => $date,
            ':created_at' => date('Y-m-d H:i:s')
        ]);

        return (int) $this->db->pdo->lastInsertId();
    }

    /**
     * Soft delete warning
     */
    public function delete(int $warningId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE peringatan_mhs 
            SET deleted_at = :deleted_at 
            WHERE id_peringatan_mhs = :id
        ");
        $stmt->execute([
            ':deleted_at' => date('Y-m-d H:i:s'),
            ':id' => $warningId
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Find warning by ID
     */
    public function findById(int $warningId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM peringatan_mhs 
            WHERE id_peringatan_mhs = :id
        ");
        $stmt->execute([':id' => $warningId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all warnings for a user
     */
    public function getByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                pm.id_peringatan_mhs,
                pm.tgl_peringatan,
                ps.nama_peringatan,
                pm.created_at
            FROM peringatan_mhs pm
            LEFT JOIN peringatan_suspensi ps ON pm.id_peringatan = ps.id_peringatan
            WHERE pm.id_akun = :user_id 
            AND pm.deleted_at IS NULL
            ORDER BY pm.tgl_peringatan DESC
        ");
        $stmt->execute([':user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count warnings for a user
     */
    public function countByUserId(int $userId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM peringatan_mhs 
            WHERE id_akun = :user_id 
            AND deleted_at IS NULL
        ");
        $stmt->execute([':user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get users with warning count >= threshold
     */
    public function getUsersWithWarningCount(int $threshold = 3): array
    {
        $stmt = $this->db->prepare("
            SELECT pm.id_akun, COUNT(*) as warning_count
            FROM peringatan_mhs pm
            LEFT JOIN users u ON pm.id_akun = u.id_user
            WHERE pm.deleted_at IS NULL
            AND u.status != 'suspended'
            GROUP BY pm.id_akun
            HAVING warning_count >= :threshold
        ");
        $stmt->execute([':threshold' => $threshold]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get warning type by name
     */
    public function getWarningTypeByName(string $pattern): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id_peringatan, nama_peringatan 
            FROM peringatan_suspensi 
            WHERE nama_peringatan LIKE :pattern 
            AND deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([':pattern' => "%{$pattern}%"]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all warning types
     */
    public function getWarningTypes(): array
    {
        $stmt = $this->db->pdo->query("
            SELECT * FROM peringatan_suspensi 
            WHERE deleted_at IS NULL
            ORDER BY id_peringatan ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new warning type
     */
    public function createWarningType(string $name): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO peringatan_suspensi (nama_peringatan, created_at)
            VALUES (:nama, NOW())
        ");
        $stmt->execute([':nama' => $name]);
        return (int) $this->db->pdo->lastInsertId();
    }

    /**
     * Update warning type
     */
    public function updateWarningType(int $id, string $name): bool
    {
        $stmt = $this->db->prepare("
            UPDATE peringatan_suspensi 
            SET nama_peringatan = :nama, updated_at = NOW()
            WHERE id_peringatan = :id
        ");
        $stmt->execute([':nama' => $name, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Delete warning type (soft delete)
     */
    public function deleteWarningType(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE peringatan_suspensi 
            SET deleted_at = NOW()
            WHERE id_peringatan = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Get warning type by ID
     */
    public function getWarningTypeById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM peringatan_suspensi 
            WHERE id_peringatan = :id AND deleted_at IS NULL
        ");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Get all warnings with user info (for admin view) - PAGINATED
     */
    public function getAllPaginated(int $perPage = 15, int $page = 1): \App\Core\Paginator
    {
        $offset = ($page - 1) * $perPage;

        // Get total count
        $countStmt = $this->db->pdo->query("
            SELECT COUNT(*) FROM peringatan_mhs WHERE deleted_at IS NULL
        ");
        $total = (int) $countStmt->fetchColumn();

        // Get items
        $stmt = $this->db->prepare("
            SELECT 
                pm.id_peringatan_mhs,
                pm.id_akun,
                pm.tgl_peringatan,
                pm.created_at,
                ps.nama_peringatan,
                u.nama as user_nama,
                u.email as user_email,
                u.nim,
                u.nip
            FROM peringatan_mhs pm
            LEFT JOIN peringatan_suspensi ps ON pm.id_peringatan = ps.id_peringatan
            LEFT JOIN users u ON pm.id_akun = u.id_user
            WHERE pm.deleted_at IS NULL
            ORDER BY pm.tgl_peringatan DESC, pm.created_at DESC
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
     * Get warning types paginated
     */
    public function getWarningTypesPaginated(int $perPage = 15, int $page = 1): \App\Core\Paginator
    {
        $offset = ($page - 1) * $perPage;

        // Get total count
        $countStmt = $this->db->pdo->query("
            SELECT COUNT(*) FROM peringatan_suspensi WHERE deleted_at IS NULL
        ");
        $total = (int) $countStmt->fetchColumn();

        // Get items
        $stmt = $this->db->prepare("
            SELECT * FROM peringatan_suspensi 
            WHERE deleted_at IS NULL
            ORDER BY id_peringatan ASC
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
     * Get all warnings with user info (for admin view) - legacy non-paginated
     */
    public function getAll(int $limit = 100, int $offset = 0): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                pm.id_peringatan_mhs,
                pm.id_akun,
                pm.tgl_peringatan,
                pm.created_at,
                ps.nama_peringatan,
                u.nama as user_nama,
                u.email as user_email,
                u.nim,
                u.nip
            FROM peringatan_mhs pm
            LEFT JOIN peringatan_suspensi ps ON pm.id_peringatan = ps.id_peringatan
            LEFT JOIN users u ON pm.id_akun = u.id_user
            WHERE pm.deleted_at IS NULL
            ORDER BY pm.tgl_peringatan DESC, pm.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
