<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\WarningRepository;
use App\Repositories\SuspensionRepository;
use App\Core\Paginator;
use App\Services\Logger;
use App\Models\Role;
use App\Models\User;
use Exception;

class UserService
{
    public function __construct(
        private UserRepository $userRepo,
        private WarningRepository $warningRepo,
        private SuspensionRepository $suspensionRepo,
        private Logger $logger,
        private ?EmailService $emailService = null
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

    // ==================== WARNINGS & SUSPENSIONS ====================

    /**
     * Add warning to user and auto-suspend if reaches 3 warnings
     */
    public function addWarning(int $userId, int $peringatanId, ?string $reason = null): void
    {
        $user = $this->userRepo->findById($userId);
        if (!$user) {
            throw new Exception('User tidak ditemukan');
        }

        // Add warning via repository (insert into peringatan_mhs)
        $this->warningRepo->create($userId, $peringatanId, date('Y-m-d'));

        // Update users.peringatan column (increment)
        $newCount = $user->peringatan + 1;
        $this->userRepo->update($userId, [
            'peringatan' => $newCount
        ]);

        // Get warning type name for email
        $warningType = $this->warningRepo->getWarningTypeById($peringatanId);
        $warningTypeName = $warningType['nama_peringatan'] ?? 'Peringatan';

        $this->logger->info('Warning added to user', [
            'user_id' => $userId,
            'peringatan_id' => $peringatanId,
            'reason' => $reason
        ]);

        // Send warning email notification
        if ($this->emailService) {
            try {
                $this->emailService->sendWarningNotification(
                    $user->email,
                    $user->nama,
                    $warningTypeName,
                    $reason ?? '',
                    $newCount
                );
            } catch (Exception $e) {
                $this->logger->error('Failed to send warning email', [
                    'user_id' => $userId,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Check if user should be auto-suspended (3 or more warnings)
        if ($newCount >= 3 && $user->status !== 'suspended') {
            $suspendUntil = date('Y-m-d', strtotime('+7 days'));
            $this->suspendUser($userId);

            // Add suspension record via repository
            $this->suspensionRepo->create($userId, date('Y-m-d'));

            $this->logger->warning('User auto-suspended due to 3 warnings', ['user_id' => $userId]);

            // Send suspension email
            if ($this->emailService) {
                try {
                    $this->emailService->sendSuspensionNotification(
                        $user->email,
                        $user->nama,
                        $suspendUntil
                    );
                } catch (Exception $e) {
                    $this->logger->error('Failed to send suspension email', [
                        'user_id' => $userId,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    /**
     * Remove warning from user
     */
    public function removeWarning(int $warningId): void
    {
        // Get user ID from warning before deleting
        $warning = $this->warningRepo->findById($warningId);

        $deleted = $this->warningRepo->delete($warningId);

        if (!$deleted) {
            throw new Exception('Peringatan tidak ditemukan');
        }

        // Update users.peringatan column (decrement, but never below 0)
        if ($warning && isset($warning['id_akun'])) {
            $user = $this->userRepo->findById($warning['id_akun']);
            if ($user && $user->peringatan > 0) {
                $this->userRepo->update($warning['id_akun'], [
                    'peringatan' => $user->peringatan - 1
                ]);
            }
        }

        $this->logger->info('Warning removed', ['warning_id' => $warningId]);
    }

    /**
     * Get all warnings for a user
     */
    public function getUserWarnings(int $userId): array
    {
        return $this->warningRepo->getByUserId($userId);
    }

    /**
     * Get all warnings across all users (for admin view) - PAGINATED
     */
    public function getAllWarningsPaginated(int $perPage = 15, int $page = 1): \App\Core\Paginator
    {
        return $this->warningRepo->getAllPaginated($perPage, $page);
    }

    /**
     * Get all suspensions (for admin view) - PAGINATED
     */
    public function getAllSuspensionsPaginated(int $perPage = 15, int $page = 1): \App\Core\Paginator
    {
        return $this->suspensionRepo->getAllPaginated($perPage, $page);
    }

    /**
     * Get all warnings across all users (for admin view) - legacy
     */
    public function getAllWarnings(): array
    {
        return $this->warningRepo->getAll();
    }

    /**
     * Get all suspensions (for admin view) - legacy
     */
    public function getAllSuspensions(): array
    {
        return $this->suspensionRepo->getAll();
    }

    // ==================== WARNING TYPES CRUD ====================

    public function getWarningTypes(): array
    {
        return $this->warningRepo->getWarningTypes();
    }

    public function getWarningTypesPaginated(int $perPage = 15, int $page = 1): \App\Core\Paginator
    {
        return $this->warningRepo->getWarningTypesPaginated($perPage, $page);
    }

    public function createWarningType(string $name): int
    {
        return $this->warningRepo->createWarningType($name);
    }

    public function updateWarningType(int $id, string $name): bool
    {
        return $this->warningRepo->updateWarningType($id, $name);
    }

    public function deleteWarningType(int $id): bool
    {
        return $this->warningRepo->deleteWarningType($id);
    }
}
