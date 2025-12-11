<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Role;

class RoleRepository
{
    public function __construct(private Database $database)
    {
    }

    public function findIdByName(string $name): ?int
    {
        if (!$name) {
            return null;
        }

        $role = Role::Query()->where('nama_role', $name)->first();

        return $role?->id_role;
    }

    public function findNameById(?int $idRole): ?string
    {
        if (!$idRole) {
            return null;
        }

        $role = Role::Query()->where('id_role', $idRole)->first();

        return $role?->nama_role;
    }

    public function getAll(): array
    {
        return Role::Query()->get();
    }

    public function getAllRoleName(): array
    {
        return Role::Query()->pluck('nama_role');
    }

    public function findById(int $id): ?Role
    {
        return Role::Query()->where('id_role', $id)->first();
    }
}