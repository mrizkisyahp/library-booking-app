<?php

namespace App\Models;

use App\Core\DbModel;
use App\Core\App;

class Booking extends DbModel
{
    public ?int $id_booking = null;
    public ?int $user_id = null;
    public ?int $ruangan_id = null;
    public string $tanggal_booking = '';
    public string $tanggal_penggunaan_ruang = '';
    public string $waktu_mulai = '';
    public string $waktu_selesai = '';
    public string $tujuan = '';
    public string $status = 'draft'; // draft, pending, verified, active, completed, cancelled, expired, no_show
    public ?string $alasan_reject = null;
    public ?string $checkin_code = null;
    public ?string $invite_token = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public ?string $nama_ruangan = null;
    public ?string $jenis_ruangan = null;
    public ?int $required_members = null;
    public ?int $maximum_members = null;
    public ?int $current_members = null;
    public ?int $id_feedback = null;
    public ?string $nama = null;
    public ?bool $has_been_rescheduled = null;
    public ?string $surat_path = null;
    public ?string $deleted_at = null;

    public static function tableName(): string
    {
        return 'booking';
    }

    public static function primaryKey(): string
    {
        return 'id_booking';
    }

    public function attributes(): array
    {
        return [
            'id_booking',
            'user_id',
            'ruangan_id',
            'tanggal_booking',
            'tanggal_penggunaan_ruang',
            'waktu_mulai',
            'waktu_selesai',
            'tujuan',
            'status',
            'alasan_reject',
            'checkin_code',
            'invite_token',
            'has_been_rescheduled',
            'surat_path',
            'deleted_at',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'ruangan_id', 'id_ruangan');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'anggota_booking', 'booking_id', 'user_id', 'id_booking');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class, 'booking_id', 'id_booking');
    }

    public function rules(): array
    {
        return [];
    }

    // public function getMembers(): array
    // {
    //     $stmt = App::$app->db->prepare("
    //         SELECT 
    //             ab.id_anggota,
    //             ab.booking_id,
    //             u.id_user,
    //             u.nama,
    //             u.email,
    //             ab.created_at AS joined_at,
    //             0 AS is_owner
    //         FROM anggota_booking ab
    //         JOIN users u ON u.id_user = ab.user_id
    //         WHERE ab.booking_id = :id
    //         ORDER BY u.nama ASC
    //     ");

    //     $stmt->bindValue(':id', $this->id_booking, \PDO::PARAM_INT);
    //     $stmt->execute();

    //     $members = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    //     $ownerIncluded = false;

    //     foreach ($members as $m) {
    //         if ((int) $m['id_user'] === (int) $this->user_id) {
    //             $ownerIncluded = true;
    //             break;
    //         }
    //     }

    //     if (!$ownerIncluded) {
    //         $pic = User::Query()->where('id_user', $this->user_id)->first();
    //         if ($pic) {
    //             $members[] = [
    //                 'id_anggota' => null,
    //                 'booking_id' => $this->id_booking,
    //                 'id_user' => $pic->id_user,
    //                 'nama' => $pic->nama,
    //                 'email' => $pic->email,
    //                 'joined_at' => $pic->created_at,
    //                 'is_owner' => 1,
    //             ];
    //         }
    //     }

    //     usort($members, function ($a, $b) {
    //         if ($a['is_owner'] === $b['is_owner']) {
    //             return strcmp($a['nama'], $b['nama']);
    //         }
    //         return $a['is_owner'] ? -1 : 1;
    //     });

    //     return $members;
    // }

    // public static function search(array $filters = []): array
    // {
    //     [$sql, $params] = self::buildQuery($filters);

    //     $stmt = App::$app->db->prepare($sql . ' ORDER BY booking.id_booking DESC');
    //     foreach ($params as $key => $value) {
    //         $stmt->bindValue($key, $value);
    //     }
    //     $stmt->execute();

    //     return $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);
    // }

    // public static function findPaginated(int $page, int $perPage, array $filters = [])
    // {
    //     $offset = ($page - 1) * $perPage;
    //     [$baseSql, $params] = self::buildQuery($filters);

    //     $sql = $baseSql . " ORDER by booking.id_booking DESC LIMIT :limit OFFSET :offset";

    //     $stmt = App::$app->db->prepare($sql);
    //     foreach ($params as $key => $value) {
    //         $stmt->bindValue($key, $value);
    //     }

    //     $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
    //     $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
    //     $stmt->execute();

    //     return $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);
    // }

    // public static function findPaginatedMyBooking(int $userid, int $page, int $PerPage, array $filters = [])
    // {
    //     $offset = ($page - 1) * $PerPage;
    //     [$baseSql, $params] = self::buildQueryMyBooking($userid, $filters);

    //     $sql = $baseSql . " ORDER by booking.id_booking DESC LIMIT :limit OFFSET :offset";

    //     $stmt = App::$app->db->prepare($sql);
    //     foreach ($params as $key => $value) {
    //         $stmt->bindValue($key, $value);
    //     }

    //     $stmt->bindValue(':limit', $PerPage, \PDO::PARAM_INT);
    //     $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
    //     $stmt->execute();

    //     return $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);
    // }

    // private static function buildQueryMyBooking(int $userid, array $filters): array
    // {
    //     $sql = "
    //         SELECT booking.*, users.nama, ruangan.nama_ruangan, id_feedback 
    //         FROM booking
    //         LEFT JOIN users ON booking.user_id = users.id_user
    //         LEFT JOIN ruangan ON booking.ruangan_id = ruangan.id_ruangan
    //         LEFT JOIN feedback ON booking.id_booking = feedback.booking_id
    //         WHERE booking.user_id = :user_id
    //     ";
    //     $params = [
    //         ':user_id' => $userid
    //     ];

    //     if (!empty($filters['keyword'])) {
    //         $sql .= " AND (
    //             users.nama LIKE :keyword OR
    //             ruangan.nama_ruangan LIKE :keyword
    //         )";
    //         $params[':keyword'] = '%' . $filters['keyword'] . '%';
    //     }

    //     if (!empty($filters['status'])) {
    //         $sql .= " AND booking.status = :status";
    //         $params[':status'] = $filters['status'];
    //     }

    //     if (!empty($filters['tanggal_penggunaan_ruang'])) {
    //         $sql .= " AND booking.tanggal_penggunaan_ruang = :tanggal_penggunaan_ruang";
    //         $params[':tanggal_penggunaan_ruang'] = $filters['tanggal_penggunaan_ruang'];
    //     }

    //     return [$sql, $params];
    // }

    // private static function buildQuery(array $filters): array
    // {
    //     $sql = "
    //         SELECT booking.*, users.nama, ruangan.nama_ruangan, id_feedback 
    //         FROM booking
    //         LEFT JOIN users ON booking.user_id = users.id_user
    //         LEFT JOIN ruangan ON booking.ruangan_id = ruangan.id_ruangan
    //         LEFT JOIN feedback ON booking.id_booking = feedback.booking_id
    //         WHERE 1=1
    //     ";
    //     $params = [];

    //     if (!empty($filters['keyword'])) {
    //         $sql .= " AND (
    //             users.nama LIKE :keyword OR
    //             ruangan.nama_ruangan LIKE :keyword
    //             -- booking.tanggal_booking LIKE :keyword OR
    //             -- booking.tanggal_penggunaan_ruang LIKE :keyword OR
    //             -- booking.waktu_mulai LIKE :keyword OR
    //             -- booking.waktu_selesai LIKE :keyword OR
    //         )";
    //         $params[':keyword'] = '%' . $filters['keyword'] . '%';
    //     }

    //     if (!empty($filters['status'])) {
    //         $sql .= " AND booking.status = :status";
    //         $params[':status'] = $filters['status'];
    //     }

    //     return [$sql, $params];
    // }

    // public static function count(array $filters = []): int
    // {
    //     [$baseSql, $params] = self::buildQuery($filters);
    //     $sql = "SELECT COUNT(*) FROM ({$baseSql}) AS filtered";

    //     $stmt = App::$app->db->prepare($sql);
    //     foreach ($params as $key => $value) {
    //         $stmt->bindValue($key, $value);
    //     }
    //     $stmt->execute();

    //     return (int) $stmt->fetchColumn();
    // }
}
