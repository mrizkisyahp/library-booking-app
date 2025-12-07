<?php

namespace App\Models;

use App\Core\App;
use App\Core\DbModel;

class Room extends DbModel
{
    public ?int $id_ruangan = null;
    public string $nama_ruangan = '';
    public ?int $kapasitas_min = null;
    public ?int $kapasitas_max = null;
    public string $jenis_ruangan = '';
    public string $deskripsi_ruangan = '';
    public string $status_ruangan = 'available';
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public ?string $deleted_at = null;
    public ?bool $requires_special_approval = false;

    public static function tableName(): string
    {
        return 'ruangan';
    }

    public static function primaryKey(): string
    {
        return 'id_ruangan';
    }

    public function attributes(): array
    {
        return [
            'id_ruangan',
            'nama_ruangan',
            'kapasitas_min',
            'kapasitas_max',
            'jenis_ruangan',
            'deskripsi_ruangan',
            'status_ruangan',
            'created_at',
            'updated_at',
            'deleted_at',
            'requires_special_approval',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'ruangan_id', 'id_ruangan');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'room_id', 'id_ruangan');
    }

    public function rules(): array
    {
        return [];
    }


    // public static function search(array $filters = []): array
    // {
    //     [$sql, $params] = self::buildQuery($filters, [
    //         'only_available' => true,
    //     ]);

    //     $stmt = App::$app->db->prepare($sql . " ORDER BY nama_ruangan ASC");
    //     foreach ($params as $key => $value) {
    //         $stmt->bindValue($key, $value);
    //     }
    //     $stmt->execute();

    //     return $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);
    // }

    // public static function findPaginated(int $page, int $perPage, array $filters = [], array $options = [])
    // {
    //     $offset = ($page - 1) * $perPage;

    //     $options = array_merge([
    //         'only_available' => false,
    //         'order' => 'ruangan.id_ruangan DESC',
    //     ], $options);

    //     [$baseSql, $params] = self::buildQuery($filters, $options);
    //     $sql = $baseSql . " ORDER BY {$options['order']} LIMIT :limit OFFSET :offset";

    //     $stmt = App::$app->db->prepare($sql);
    //     foreach ($params as $key => $value) {
    //         $stmt->bindValue($key, $value);
    //     }
    //     $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
    //     $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
    //     $stmt->execute();

    //     return $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);
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

    // private static function buildQuery(array $filters, array $options = []): array
    // {
    //     $options = array_merge([
    //         'only_available' => false,
    //     ], $options);

    //     $sql = "SELECT * FROM ruangan WHERE 1=1";
    //     $params = [];

    //     if (!empty($filters['keyword'])) {
    //         $sql .= " AND (
    //             nama_ruangan LIKE :keyword
    //         )";
    //         $params[':keyword'] = '%' . $filters['keyword'] . '%';
    //     }

    //     if (!empty($filters['nama_ruangan'])) {
    //         $sql .= " AND nama_ruangan LIKE :nama";
    //         $params[':nama'] = '%' . $filters['nama_ruangan'] . '%';
    //     }

    //     if (!empty($filters['jenis_ruangan'])) {
    //         $sql .= " AND jenis_ruangan LIKE :jenis";
    //         $params[':jenis'] = '%' . $filters['jenis_ruangan'] . '%';
    //     }

    //     if (!empty($filters['kapasitas_min'])) {
    //         $sql .= " AND kapasitas_min <= :kapasitas_min";
    //         $params[':kapasitas_min'] = (int) $filters['kapasitas_min'];
    //     }

    //     if (!empty($filters['kapasitas_max'])) {
    //         $sql .= " AND kapasitas_max >= :kapasitas_max";
    //         $params[':kapasitas_max'] = (int) $filters['kapasitas_max'];
    //     }

    //     if (!empty($filters['status_ruangan'])) {
    //         $sql .= " AND status_ruangan = :status";
    //         $params[':status'] = $filters['status_ruangan'];
    //     } elseif (!empty($options['only_available'])) {
    //         $sql .= " AND status_ruangan = 'available'";
    //     }

    //     return [$sql, $params];
    // }

    // public function isAvailable(): bool
    // {
    //     return $this->status_ruangan === 'available';
    // }

    // public static function getAvailableRooms(): array
    // {
    //     $stmt = App::$app->db->prepare("
    //         SELECT * FROM ruangan
    //         WHERE status_ruangan = 'available'
    //         ORDER BY nama_ruangan ASC
    //     ");
    //     $stmt->execute();

    //     return $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);
    // }

    // public function getFacilities(): array
    // {
    //     if (empty($this->deskripsi_ruangan)) {
    //         return [];
    //     }

    //     $parts = preg_split('/[\r\n;,]+/', $this->deskripsi_ruangan);
    //     return array_values(array_filter(array_map('trim', $parts)));
    // }

    // public function getPhotoDataUris(): array
    // {
    //     $dir = App::$ROOT_DIR . '/Public/uploads/Room_Photos/';
    //     $slug = $this->slugify($this->nama_ruangan);
    //     $pattern = $dir . $slug . '_*.{jpg,jpeg,png,webp,svg}';
    //     $files = glob($pattern, GLOB_BRACE) ?: [];
    //     sort($files);

    //     $photos = [];
    //     foreach ($files as $file) {
    //         $mime = match (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
    //             'jpg', 'jpeg' => 'image/jpeg',
    //             'png' => 'image/png',
    //             'webp' => 'image/webp',
    //             'svg' => 'image/svg+xml',
    //             default => 'application/octet-stream',
    //         };
    //         $photos[] = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file));
    //     }

    //     return $photos;
    // }

    // public function getThumbnail(): ?string
    // {
    //     $photos = $this->getPhotoDataUris();
    //     return $photos[0] ?? null;
    // }

    // public function getAvailabilityCalendar(int $days = 5): array
    // {
    //     $start = date('Y-m-d');
    //     $stmt = App::$app->db->prepare("
    //         SELECT tanggal_penggunaan_ruang AS tanggal, waktu_mulai, waktu_selesai, status 
    //         FROM booking
    //         WHERE ruangan_id = :room
    //           AND tanggal_penggunaan_ruang BETWEEN :start AND :end
    //           AND status NOT IN ('draft', 'cancelled', 'noshow')
    //         ORDER BY tanggal_penggunaan_ruang ASC, waktu_mulai ASC
    //     ");
    //     $stmt->bindValue(':room', $this->id_ruangan, \PDO::PARAM_INT);
    //     $stmt->bindValue(':start', $start);
    //     $stmt->bindValue(':end', date('Y-m-d', strtotime('+21 days')));
    //     $stmt->execute();

    //     $calendar = [];
    //     $added = 0;
    //     $offset = 0;
    //     while ($added < $days) {
    //         $date = date('Y-m-d', strtotime("+{$offset} days"));
    //         $offset++;

    //         $day = (int) date('N', strtotime($date));
    //         if ($day === 6 || $day === 7) {
    //             continue;
    //         }

    //         $calendar[$date] = [];
    //         $added++;
    //     }

    //     foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
    //         if (isset($calendar[$row['tanggal']])) {
    //             $calendar[$row['tanggal']][] = $row;
    //         }
    //     }

    //     return $calendar;
    // }

    // public function slugify(string $name): string
    // {
    //     $slug = preg_replace('/[^A-Za-z0-9]+/', '_', $name);
    //     return trim($slug, '_');
    // }

    // public function isRoomOccupied(int $roomId, int $dateAt, int $startAt, int $endAt): bool
    // {
    //     $stmt = App::$app->db->prepare("
    //         SELECT * FROM booking WHERE ruangan_id = :room AND tanggal_penggunaan_ruang = :date AND waktu_mulai <= :end AND waktu_selesai >= :start
    //         AND status NOT IN ('draft', 'cancelled', 'noshow', 'pending')
    //     ");
    //     $stmt->bindValue(':room', $roomId, \PDO::PARAM_INT);
    //     $stmt->bindValue(':date', $dateAt, \PDO::PARAM_INT);
    //     $stmt->bindValue(':start', $startAt, \PDO::PARAM_INT);
    //     $stmt->bindValue(':end', $endAt, \PDO::PARAM_INT);
    //     $stmt->execute();

    //     return $stmt->rowCount() > 0;
    // }
}
