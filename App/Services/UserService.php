<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Core\Paginator;
use App\Services\Logger;
use App\Models\Role;
use Exception;

class UserService
{
    public function __construct(
        private UserRepository $userRepo,
        private Logger $logger
    ) {
    }

    // ==================== GET DATA ====================

    public function getAllUsers(array $filters, int $perPage, int $page): Paginator
    {
        return $this->userRepo->getAllUsers($filters, $perPage, $page);
    }

    public function getPendingKubacaWithImage(array $filters, int $perPage, int $page): Paginator
    {
        return $this->userRepo->getPendingKubacaWithImage($filters, $perPage, $page);
    }

    public function getPendingKubacaWithImageCount(): int
    {
        return $this->userRepo->getPendingKubacaWithImageCount();
    }

    public function getUserById(int $id): ?object
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            throw new Exception('User tidak ditemukan');
        }
        return $user;
    }

    public function getAllRoles(): array
    {
        return Role::Query()->orderBy('nama_role')->get();
    }

    public function getAllStatuses(): array
    {
        return ['active', 'pending kubaca', 'pending verification', 'suspended', 'rejected', 'nonaktif'];
    }

    public function getStats(): array
    {
        return [
            'total' => $this->userRepo->getTotalUsers(),
            'active' => $this->userRepo->getActiveUsers(),
            'pending' => $this->userRepo->getPendingKubacaUsers(),
            'suspended' => $this->userRepo->getSuspendedUsers(),
        ];
    }

    // ==================== CREATE/UPDATE ====================

    public function createUser(array $data): void
    {
        if (empty($data['email']) || empty($data['password']) || empty($data['nama'])) {
            throw new Exception('Email, password, dan nama harus diisi');
        }

        if ($this->userRepo->existsByEmail($data['email'])) {
            throw new Exception('Email sudah terdaftar');
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['status'] = 'active';

        $this->userRepo->create($data);

        $this->logger->info('Admin created new user', ['email' => $data['email']]);
    }

    public function updateUser(int $id, array $data): void
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            throw new Exception('User tidak ditemukan');
        }

        // If password empty, don't update
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $this->userRepo->update($id, $data);

        $this->logger->info('Admin updated user', ['user_id' => $id]);
    }

    public function deleteUser(int $id): void
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            throw new Exception('User tidak ditemukan');
        }

        $this->userRepo->delete($id);

        $this->logger->info('Admin deleted user', ['user_id' => $id]);
    }

    // ==================== SUSPEND/UNSUSPEND ====================

    public function suspendUser(int $id): void
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            throw new Exception('User tidak ditemukan');
        }

        if ($user->status === 'suspended') {
            throw new Exception('User sudah disuspend');
        }

        $this->userRepo->update($id, ['status' => 'suspended']);

        $this->logger->info('Admin suspended user', ['user_id' => $id]);
    }

    public function unsuspendUser(int $id): void
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            throw new Exception('User tidak ditemukan');
        }

        if ($user->status !== 'suspended') {
            throw new Exception('User tidak dalam status suspended');
        }

        $this->userRepo->update($id, ['status' => 'active']);

        $this->logger->info('Admin unsuspended user', ['user_id' => $id]);
    }

    // ==================== PASSWORD ====================

    public function resetPassword(int $id): string
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            throw new Exception('User tidak ditemukan');
        }

        $newPassword = bin2hex(random_bytes(4));
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $this->userRepo->update($id, ['password' => $hashedPassword]);

        $this->logger->info('Admin reset password for user', ['user_id' => $id]);

        return $newPassword;
    }

    // ==================== KUBACA ====================

    public function approveKubaca(int $id, string $masa_aktif): void
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            throw new Exception('User tidak ditemukan');
        }

        if ($user->status !== 'pending kubaca') {
            throw new Exception('User tidak dalam status pending kubaca');
        }

        $this->userRepo->update($id, ['status' => 'active', 'masa_aktif' => $masa_aktif, 'alasan_reject' => null]);

        $this->logger->info('Admin approved kubaca for user', ['user_id' => $id]);
    }

    public function rejectKubaca(int $id, string $reason = ''): void
    {
        $user = $this->userRepo->findById($id);
        if (!$user) {
            throw new Exception('User tidak ditemukan');
        }

        if ($user->status !== 'pending kubaca') {
            throw new Exception('User tidak dalam status pending kubaca');
        }

        $this->userRepo->update($id, [
            'status' => 'rejected',
            'alasan_reject' => $reason,
        ]);

        $this->logger->info('Admin rejected kubaca for user', ['user_id' => $id, 'reason' => $reason]);
    }
}
