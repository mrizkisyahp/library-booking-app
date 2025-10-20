<?php

class m0003_create_bookings_table {

    public function up() {
        $db = \App\Core\App::$app->db;
        $sql = "CREATE TABLE IF NOT EXISTS `bookings` (
            `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` INT(11) UNSIGNED NOT NULL,
            `room_id` INT(11) UNSIGNED NOT NULL,
            `booking_date` DATE NOT NULL,
            `start_time` TIME NOT NULL,
            `end_time` TIME NOT NULL,
            `participants` INT(11) NOT NULL,
            `purpose` TEXT NOT NULL,
            `image` VARCHAR(255) NULL,
            `status` ENUM('pending', 'validated', 'active', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
            `booking_code` VARCHAR(20) NULL UNIQUE,
            `check_in_time` DATETIME NULL,
            `check_out_time` DATETIME NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE,
            INDEX `idx_user_id` (`user_id`),
            INDEX `idx_room_id` (`room_id`),
            INDEX `idx_booking_date` (`booking_date`),
            INDEX `idx_status` (`status`),
            INDEX `idx_booking_code` (`booking_code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $db->pdo->exec($sql);
    }

    public function down() {
        $db = \App\Core\App::$app->db;
        $sql = "DROP TABLE IF EXISTS `bookings`;";
        $db->pdo->exec($sql);
    }
}