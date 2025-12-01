<?php

namespace App\Models;

use App\Core\DbModel;
use App\Core\App;

class User extends DbModel
{
    public const SCENARIO_REGISTER = 'register';
    public const SCENARIO_LOGIN = 'login';
    public const SCENARIO_UPDATE = 'update';
    public const SCENARIO_VERIFY_OTP = 'verify_otp';
    public const SCENARIO_RESET_REQUEST = 'reset_request';
    public const SCENARIO_RESET_PASSWORD = 'reset_password';
    public ?int $id_user = null;
    public string $nama = '';
    public ?string $nim = null;
    public ?string $nip = null;
    public string $email = '';
    public string $password = '';
    public string $confirm_password = '';
    public string $code = '';
    public string $new_password = '';
    public string $confirm_new_password = '';
    public ?int $id_role = null;
    public ?string $nama_role = null;
    public ?string $kubaca_img = null;
    public int $peringatan = 0;
    public string $status = 'pending';
    public ?string $jurusan = null;
    public ?string $nomor_hp = null;
    public ?string $suspensi_terakhir = null;
    public ?string $masa_aktif = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public string $identifier = '';
    public string $scenario = self::SCENARIO_REGISTER;

    public static function tableName(): string
    {
        return 'users';
    }

    public static function primaryKey(): string
    {
        return 'id_user';
    }

    public function setScenario(string $scenario): void
    {
        $this->scenario = $scenario;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id', 'id_user');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'user_id', 'id_user');
    }

    public function rules(): array
    {
        return [];
    }

    public function attributes(): array
    {
        return [
            'id_user',
            'nama',
            'nim',
            'nip',
            'email',
            'password',
            'id_role',
            'kubaca_img',
            'peringatan',
            'status',
            'jurusan',
            'nomor_hp',
            'created_at',
            'updated_at',
        ];
    }

    public function save(): bool
    {
        return parent::save();
    }

    public function getDisplayName(): string
    {
        return $this->nama;
    }

    public function isAdmin(): bool
    {
        return (string) $this->id_role === '1';
    }

    public function isDosen(): bool
    {
        return (string) $this->id_role === '2';
    }

    public function isMahasiswa(): bool
    {
        return (string) $this->id_role === '3';
    }

    public static function search(array $filters = []): array
    {
        [$sql, $params] = self::buildQuery($filters);

        $stmt = App::$app->db->prepare($sql . " ORDER BY users.id_user DESC");
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);
    }

    public static function findPaginated(int $page, int $perPage, array $filters = [])
    {
        $offset = ($page - 1) * $perPage;
        [$baseSql, $params] = self::buildQuery($filters);

        $sql = $baseSql . " ORDER BY users.id_user DESC LIMIT :limit OFFSET :offset";

        $stmt = App::$app->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);
    }

    private static function buildQuery(array $filters): array
    {
        $sql = "
        SELECT users.*, role.nama_role
        FROM users
        LEFT JOIN role ON users.id_role = role.id_role
        WHERE 1=1
    ";
        $params = [];

        if (!empty($filters['keyword'])) {
            $sql .= " AND (
            users.nama LIKE :keyword OR
            users.email LIKE :keyword OR
            users.nim LIKE :keyword OR
            users.nip LIKE :keyword
        )";
            $params[':keyword'] = '%' . $filters['keyword'] . '%';
        }

        if (!empty($filters['role'])) {
            $sql .= " AND role.nama_role = :role";
            $params[':role'] = (string) $filters['role'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND users.status = :status";
            $params[':status'] = $filters['status'];
        }

        return [$sql, $params];
    }

    public static function count(array $filters = []): int
    {
        [$baseSql, $params] = self::buildQuery($filters);
        $sql = "SELECT COUNT(*) FROM ({$baseSql}) AS filtered";

        $stmt = App::$app->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public static function countActive(): int
    {
        $db = App::$app->db;
        $stmt = $db->prepare("select COUNT(*) AS COUNT from users where status = 'active'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function countPending(): int
    {
        $db = App::$app->db;
        $stmt = $db->prepare("select COUNT(*) AS COUNT from users where status = 'pending kubaca'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public static function countSuspended(): int
    {
        $db = App::$app->db;
        $stmt = $db->prepare("select COUNT(*) AS COUNT from users where status = 'suspended'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

}
