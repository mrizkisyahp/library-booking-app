<?php

namespace App\Models;

use App\Core\App;
use App\Core\DbModel;

class Role extends DbModel
{

    public $id_role;
    public $nama_role;

    public static function primaryKey(): string
    {
        return 'id_role';
    }

    public static function tableName(): string
    {
        return 'role';
    }

    public function rules(): array
    {
        return [
            'id_role' => ['required', 'integer'],
            'nama_role' => ['required', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'id_role',
            'nama_role',
        ];
    }

    public static function getIdByName(?string $name): ?int
    {
        if (!$name) {
            return null;
        }

        $stmt = App::$app->db->prepare("SELECT id_role FROM role WHERE nama_role = :name LIMIT 1");
        $stmt->bindValue(':name', $name);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row['id_role'] ?? null;
    }

    public static function getNameById(?int $idRole): ?string
    {
        if (!$idRole) {
            return null;
        }

        $stmt = App::$app->db->prepare("SELECT nama_role FROM role WHERE id_role = :id LIMIT 1");
        $stmt->bindValue(':id', $idRole);
        $stmt->execute();

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row['nama_role'] ?? null;
    }

    public static function getAllRoleName(): array
    {
        $stmt = App::$app->db->prepare("SELECT * FROM role");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
