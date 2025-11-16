<?php

namespace App\Core\Services;

use App\Core\App;
use App\Models\Role;
use App\Models\User;

class AdminUserService
{
    private const DEFAULT_PER_PAGE = 20;

    private const ALLOWED_STATUSES = [
        'pending verification',
        'pending kubaca',
        'rejected',
        'active',
        'suspended',
    ];

    public function listUsers(array $filters = []): array
    {
        $page = max(1, (int)($filters['page'] ?? 1));
        $perPage = (int)($filters['perPage'] ?? self::DEFAULT_PER_PAGE);

        $queryFilters = [
            'keyword' => $filters['keyword'] ?? null,
            'role' => $filters['role'] ?? null,
            'status' => $filters['status'] ?? null,
        ];

        $users = User::findPaginated($page, $perPage, $queryFilters);

        return [
            'success' => true,
            'data' => [
                'users' => $users,
                'filters' => $queryFilters,
                'currentPage' => $page,
                'perPage' => $perPage,
                'stats' => [
                    'total' => User::count(),
                    'active' => User::countActive(),
                    'pending' => User::countPending(),
                    'suspended' => User::countSuspended(),
                ],
            ],
        ];
    }

    public function getRoles(): array
    {
        return Role::getAllRoleName();
    }

    public function getStatusOptions(): array
    {
        return self::ALLOWED_STATUSES;
    }

    public function getUserById(int $id): ?User
    {
        if ($id <= 0) {
            return null;
        }

        return User::findOne(['id_user' => $id]);
    }

    public function createUser(array $data, ?int $adminId = null): array
    {
        $user = new User();
        $user->setScenario(User::SCENARIO_REGISTER);
        $user->loadData($data);

        $user->id_role = $this->resolveRoleId($data, $user->id_role);
        if (!$user->id_role) {
            return [
                'success' => false,
                'message' => 'Please select a valid role.',
                'data' => ['model' => $user],
            ];
        }

        $user->status = $this->sanitizeStatus($data['status'] ?? null, (int)$user->id_role);
        $user->peringatan = isset($data['peringatan']) ? (int)$data['peringatan'] : 0;

        $plainPassword = trim($data['password'] ?? '');
        $confirmPassword = trim($data['confirm_password'] ?? '');

        if ($plainPassword === '') {
            $plainPassword = $this->generateRandomPassword();
            $confirmPassword = $plainPassword;
        } elseif ($confirmPassword === '') {
            $confirmPassword = $plainPassword;
        }

        $user->password = $plainPassword;
        $user->confirm_password = $confirmPassword;

        if (!$user->validate()) {
            return [
                'success' => false,
                'message' => 'Please fix the validation errors below.',
                'data' => ['model' => $user],
            ];
        }

        $user->password = password_hash($plainPassword, PASSWORD_DEFAULT);
        $user->confirm_password = $user->password;

        if (!$user->save()) {
            return [
                'success' => false,
                'message' => 'Failed to create user.',
                'data' => ['model' => $user],
            ];
        }

        if ($adminId) {
            Logger::admin('created user', $adminId, "Target: {$user->email} ({$user->status})");
        }

        EmailService::send(
            $user->email,
            $user->nama,
            'Account Created | Library Booking App',
            "<p>Hai <strong>{$user->nama}</strong>,</p><p>Akun kamu telah dibuat oleh admin. Gunakan kata sandi berikut untuk login pertama:</p><p><strong>{$plainPassword}</strong></p><p>Segera lakukan login dan ubah kata sandi untuk keamanan.</p>"
        );

        return [
            'success' => true,
            'message' => 'User created successfully.',
            'data' => ['model' => $user],
        ];
    }

    public function updateUser(int $id, array $data, ?int $adminId = null): array
    {
        $user = $this->getUserById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        $user->setScenario(User::SCENARIO_UPDATE);

        $existingHash = $user->password;
        $payload = $data;
        unset($payload['password'], $payload['confirm_password']);
        $user->loadData($payload);

        $user->id_role = $this->resolveRoleId($data, $user->id_role);
        if (!$user->id_role) {
            return [
                'success' => false,
                'message' => 'Please select a valid role.',
                'data' => ['model' => $user],
            ];
        }

        $user->status = $this->sanitizeStatus($data['status'] ?? $user->status, (int)$user->id_role);
        $user->peringatan = isset($data['peringatan']) ? (int)$data['peringatan'] : $user->peringatan;

        $newPassword = trim($data['password'] ?? '');
        if ($newPassword !== '') {
            $user->password = $newPassword;
            $user->confirm_password = trim($data['confirm_password'] ?? $newPassword);
        } else {
            $user->password = '';
            $user->confirm_password = '';
        }

        if (!$user->validate()) {
            $user->password = $existingHash;
            return [
                'success' => false,
                'message' => 'Please fix the validation errors below.',
                'data' => ['model' => $user],
            ];
        }

        if ($newPassword !== '') {
            $user->password = password_hash($newPassword, PASSWORD_DEFAULT);
        } else {
            $user->password = $existingHash;
        }

        if (!$user->save()) {
            return [
                'success' => false,
                'message' => 'Failed to update user.',
                'data' => ['model' => $user],
            ];
        }

        if ($adminId) {
            Logger::admin('updated user', $adminId, "Target: {$user->email} ({$user->status})");
        }

        return [
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => ['model' => $user],
        ];
    }

    public function deleteUser(int $id, ?int $adminId = null): array
    {
        $user = $this->getUserById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        if ($adminId && (int)$user->id_user === (int)$adminId) {
            return ['success' => false, 'message' => 'You cannot delete your own account.'];
        }

        if ((int)$user->id_role === 1 && $this->countAdmins() <= 1) {
            return ['success' => false, 'message' => 'At least one admin must remain in the system.'];
        }

        if (!$user->delete()) {
            return ['success' => false, 'message' => 'Failed to delete user.'];
        }

        if ($adminId) {
            Logger::admin('deleted user', $adminId, "Target: {$user->email}");
        }

        return ['success' => true, 'message' => 'User deleted successfully.'];
    }

    public function suspendUser(int $id, ?int $adminId = null): array
    {
        $user = $this->getUserById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        if ($user->status === 'suspended') {
            return ['success' => false, 'message' => 'User is already suspended.'];
        }

        $user->status = 'suspended';
        if (!$user->save()) {
            return ['success' => false, 'message' => 'Failed to suspend user.'];
        }

        if ($adminId) {
            Logger::admin('suspended user', $adminId, "Target: {$user->email}");
        }

        return ['success' => true, 'message' => 'User suspended successfully.'];
    }

    public function unsuspendUser(int $id, ?int $adminId = null): array
    {
        $user = $this->getUserById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        if ($user->status !== 'suspended') {
            return ['success' => false, 'message' => 'User is not suspended.'];
        }

        $user->status = 'active';
        if (!$user->save()) {
            return ['success' => false, 'message' => 'Failed to unsuspend user.'];
        }

        if ($adminId) {
            Logger::admin('unsuspended user', $adminId, "Target: {$user->email}");
        }

        return ['success' => true, 'message' => 'User unsuspended successfully.'];
    }

    public function resetPassword(int $id, ?int $adminId = null): array
    {
        $user = $this->getUserById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        $newPassword = $this->generateRandomPassword();
        $user->password = password_hash($newPassword, PASSWORD_DEFAULT);

        if (!$user->save()) {
            return ['success' => false, 'message' => 'Failed to reset password.'];
        }

        $subject = 'Password Reset | Library Booking App';
        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p>Kata sandi akun kamu telah direset oleh admin. Berikut kata sandi sementara:</p>
            <p><strong>{$newPassword}</strong></p>
            <p>Segera login dan ubah kata sandi untuk keamanan.</p>
        ";

        EmailService::send($user->email, $user->nama, $subject, $body);

        if ($adminId) {
            Logger::admin('reset user password', $adminId, "Target: {$user->email}");
        }

        return ['success' => true, 'message' => 'Password reset and emailed to user.'];
    }

    public function approveKubaca(int $id, ?int $adminId = null): array
    {
        $user = $this->getUserById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        if (!$user->kubaca_img) {
            return ['success' => false, 'message' => 'User has not uploaded KuBaca.'];
        }

        $user->status = 'active';

        if (!$user->save()) {
            return ['success' => false, 'message' => 'Failed to approve KuBaca.'];
        }

        EmailService::sendKubacaVerified($user);

        if ($adminId) {
            Logger::admin('approved kubaca', $adminId, "Target: {$user->email}");
        }

        return ['success' => true, 'message' => 'KuBaca approved and user activated.'];
    }

    public function rejectKubaca(int $id, ?string $reason = null, ?int $adminId = null): array
    {
        $user = $this->getUserById($id);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        $user->status = 'rejected';
        $user->peringatan = max(0, (int)$user->peringatan) + 1;

        if (!$user->save()) {
            return ['success' => false, 'message' => 'Failed to reject KuBaca.'];
        }

        $subject = 'KuBaca Review | Library Booking App';
        $reasonText = $reason ? "<p>Catatan admin: {$reason}</p>" : '';
        $body = "
            <p>Hai <strong>{$user->nama}</strong>,</p>
            <p>KuBaca kamu belum dapat disetujui. Silakan unggah ulang sesuai ketentuan.</p>
            {$reasonText}
            <p>Hubungi admin jika kamu butuh bantuan.</p>
        ";

        EmailService::send($user->email, $user->nama, $subject, $body);

        if ($adminId) {
            $details = "Target: {$user->email}";
            if ($reason) {
                $details .= " | Reason: {$reason}";
            }
            Logger::admin('rejected kubaca', $adminId, $details);
        }

        return ['success' => true, 'message' => 'KuBaca rejected and user notified.'];
    }

    private function resolveRoleId(array $data, ?int $fallback = null): ?int
    {
        if (!empty($data['id_role'])) {
            return (int)$data['id_role'];
        }

        if (!empty($data['role'])) {
            if (is_numeric($data['role'])) {
                return (int)$data['role'];
            }

            return Role::getIdByName($data['role']);
        }

        return $fallback;
    }

    private function sanitizeStatus(?string $status, ?int $roleId = null): string
    {
        $status = $status ? strtolower(trim($status)) : '';
        if (in_array($status, self::ALLOWED_STATUSES, true)) {
            return $status;
        }

        return match ($roleId) {
            2 => 'pending kubaca',
            3 => 'pending verification',
            default => 'active',
        };
    }

    private function countAdmins(): int
    {
        $stmt = App::$app->db->prepare('SELECT COUNT(*) FROM users WHERE id_role = 1');
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    private function generateRandomPassword(int $length = 12): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
        $charLen = strlen($characters);
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $charLen - 1)];
        }

        return $password;
    }
}
