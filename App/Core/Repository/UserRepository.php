<?php

namespace App\Core\Repository;

use App\Core\Database;
use App\Models\User;

class UserRepository
{
    public function __construct(
        private Database $database
    ) {
    }

    public function findById(int $id): ?User
    {
        return User::Query()
            ->where(User::primaryKey(), $id)
            ->with('role')
            ->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::Query()->where('email', $email)->first();
    }

    public function findByNim(string $nim): ?User
    {
        return User::Query()->where('nim', $nim)->first();
    }

    public function findByNip(string $nip): ?User
    {
        return User::Query()->where('nip', $nip)->first();
    }

    public function findByIdentifier(string $identifier): ?User
    {
        $identifier = trim($identifier);

        $user = User::Query()->where('email', $identifier)->with('role')->first();
        if ($user) {
            return $user;
        }

        $user = User::Query()->where('nim', $identifier)->with('role')->first();
        if ($user) {
            return $user;
        }

        return User::Query()->where('nip', $identifier)->with('role')->first();
    }

    public function create(array $data): User
    {
        $user = new User();

        foreach ($data as $key => $value) {
            if (property_exists($user, $key)) {
                $user->{$key} = $value;
            }
        }

        $user->save();

        return $user;
    }

    public function update(int $id, array $data): bool
    {
        $user = $this->findById($id);

        if (!$user) {
            return false;
        }

        foreach ($data as $key => $value) {
            if (property_exists($user, $key)) {
                $user->{$key} = $value;
            }
        }

        return $user->save();
    }

    public function existsByEmail(string $email): bool
    {
        return User::Query()->where('email', $email)->exists();
    }

    public function existsByNim(string $nim): bool
    {
        return User::Query()->where('nim', $nim)->exists();
    }

    public function existsByNip(string $nip): bool
    {
        return User::Query()->where('nip', $nip)->exists();
    }

    public function findByResetToken(string $hashedToken): ?User
    {
        return User::Query()
            ->where('password_reset_token', $hashedToken)
            ->where('password_reset_expires', '>', date('Y-m-d H:i:s'))
            ->with('role')
            ->first();
    }

    public function getTotalUsers(): int
    {
        return User::Query()->count();
    }

    public function getActiveUsers(): int
    {
        return User::Query()
            ->where('status', 'active')
            ->count();
    }

    public function getPendingKubacaUsers(): int
    {
        return User::Query()
            ->where('status', 'pending kubaca')
            ->count();
    }

    public function getSuspendedUsers(): int
    {
        return User::Query()
            ->where('status', 'suspended')
            ->count();
    }

    public function getRejectedUsers(): int
    {
        return User::Query()
            ->where('status', 'rejected')
            ->count();
    }

    public function getNonaktifUsers(): int
    {
        return User::Query()
            ->where('status', 'nonaktif')
            ->count();
    }

    public function getPendingVerificationEmail(): int
    {
        return User::Query()
            ->where('status', 'pending verification')
            ->count();
    }
}