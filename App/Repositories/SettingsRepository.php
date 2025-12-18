<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class SettingsRepository
{
    private PDO $db;

    public function __construct(Database $database)
    {
        $this->db = $database->pdo;
    }

    /**
     * Get a single setting value by key
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $stmt = $this->db->prepare(
            "SELECT setting_value, setting_type FROM system_settings WHERE setting_key = ?"
        );
        $stmt->execute([$key]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return $default;
        }

        return $this->castValue($row['setting_value'], $row['setting_type']);
    }

    /**
     * Set a single setting value
     */
    public function set(string $key, mixed $value): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?"
        );
        return $stmt->execute([$this->serializeValue($value), $key]);
    }

    /**
     * Get all settings as associative array
     */
    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT setting_key, setting_value, setting_type, setting_group, description 
             FROM system_settings ORDER BY setting_group, id"
        );
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = [
                'value' => $this->castValue($row['setting_value'], $row['setting_type']),
                'type' => $row['setting_type'],
                'group' => $row['setting_group'],
                'description' => $row['description'],
            ];
        }

        return $settings;
    }

    /**
     * Get settings grouped by setting_group
     */
    public function getByGroup(string $group): array
    {
        $stmt = $this->db->prepare(
            "SELECT setting_key, setting_value, setting_type, description 
             FROM system_settings WHERE setting_group = ? ORDER BY id"
        );
        $stmt->execute([$group]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $this->castValue($row['setting_value'], $row['setting_type']);
        }

        return $settings;
    }

    /**
     * Bulk update multiple settings
     */
    public function updateMany(array $settings): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE system_settings SET setting_value = ? WHERE setting_key = ?"
        );

        foreach ($settings as $key => $value) {
            $stmt->execute([$this->serializeValue($value), $key]);
        }

        return true;
    }

    /**
     * Cast value based on type
     */
    private function castValue(mixed $value, string $type): mixed
    {
        return match ($type) {
            'integer' => (int) $value,
            'boolean' => $value === '1' || $value === 'true' || $value === true,
            'json' => json_decode($value, true) ?? [],
            'time' => $value,
            default => $value,
        };
    }

    /**
     * Serialize value for storage
     */
    private function serializeValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_array($value)) {
            return json_encode($value);
        }
        return (string) $value;
    }
}
