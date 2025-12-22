<?php

class m0024_create_system_settings_table
{
    public function up()
    {
        $db = \App\Core\App::$app->db;

        $sql = "CREATE TABLE IF NOT EXISTS system_settings (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) NOT NULL UNIQUE,
            setting_value TEXT,
            setting_type ENUM('string', 'integer', 'boolean', 'json', 'time') DEFAULT 'string',
            setting_group VARCHAR(50) DEFAULT 'general',
            description VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_setting_group (setting_group)
        ) ENGINE=InnoDB 
          DEFAULT CHARSET=utf8mb4 
          COLLATE=utf8mb4_unicode_ci;";

        $db->pdo->exec($sql);

        // Insert default settings - only the ones needed for System Settings UI
        $defaults = [
            // Operating days (1=active, 0=inactive)
            ['operating_day_monday', '1', 'boolean', 'operating', 'Hari Senin aktif'],
            ['operating_day_tuesday', '1', 'boolean', 'operating', 'Hari Selasa aktif'],
            ['operating_day_wednesday', '1', 'boolean', 'operating', 'Hari Rabu aktif'],
            ['operating_day_thursday', '1', 'boolean', 'operating', 'Hari Kamis aktif'],
            ['operating_day_friday', '1', 'boolean', 'operating', 'Hari Jumat aktif'],
            ['operating_day_saturday', '0', 'boolean', 'operating', 'Hari Sabtu aktif'],
            ['operating_day_sunday', '0', 'boolean', 'operating', 'Hari Minggu aktif'],

            // Operating hours
            ['library_open_time', '08:00', 'time', 'operating', 'Jam buka perpustakaan'],
            ['library_close_time', '16:20', 'time', 'operating', 'Jam tutup perpustakaan'],

            // Break times
            ['break_start_weekday', '11:00', 'time', 'break', 'Jam mulai istirahat (Sen-Kam)'],
            ['break_end_weekday', '12:00', 'time', 'break', 'Jam selesai istirahat (Sen-Kam)'],
            ['break_start_friday', '11:00', 'time', 'break', 'Jam mulai istirahat Jumat'],
            ['break_end_friday', '13:00', 'time', 'break', 'Jam selesai istirahat Jumat'],

            // Booking duration rules
            ['min_booking_duration', '60', 'integer', 'booking', 'Durasi minimal booking (menit)'],
            ['max_booking_duration', '180', 'integer', 'booking', 'Durasi maksimal booking (menit)'],
        ];

        $stmt = $db->pdo->prepare(
            "INSERT INTO system_settings (setting_key, setting_value, setting_type, setting_group, description) 
             VALUES (?, ?, ?, ?, ?)"
        );

        foreach ($defaults as $setting) {
            $stmt->execute($setting);
        }
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;
        $sql = 'DROP TABLE IF EXISTS system_settings;';
        $db->pdo->exec($sql);
    }
}
