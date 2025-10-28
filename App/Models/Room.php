<?php

namespace App\Models;

use App\Core\DbModel;

class Room extends DbModel
{
    public ?int $id = null;
    public string $title = '';
    public int $capacity_min = 1;
    public int $capacity_max = 1;
    public ?string $description = null;
    public ?string $image = null;
    public string $status = 'available'; // available, maintenance
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function tableName(): string
    {
        return 'rooms';
    }

    public static function primaryKey(): string
    {
        return 'id';
    }

    public function rules(): array
    {
        return [
            'title' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 3]],
            'capacity_min' => [self::RULE_REQUIRED, self::RULE_NUMBER],
            'capacity_max' => [self::RULE_REQUIRED, self::RULE_NUMBER],
            'status' => [self::RULE_REQUIRED],
        ];
    }

    public function attributes(): array
    {
        return ['title', 'capacity_min', 'capacity_max', 'description', 'status'];
    }

    // apakah available roomnya
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }

    // ambil semua room
    public static function getAvailableRooms(): array
    {
        $stmt = \App\Core\App::$app->db->prepare(
            "SELECT * FROM rooms WHERE status = 'available' ORDER BY title ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS, self::class);
    }
}
