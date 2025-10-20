<?php

class m0002_create_rooms_table
{
    public function up()
    {
        $db = \App\Core\App::$app->db;
        $sql = "CREATE TABLE IF NOT EXISTS `rooms` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `title` VARCHAR(255) NOT NULL,
        `capacity_min` INT(11) NOT NULL DEFAULT 1,
        `capacity_max` INT(11) NOT NULL DEFAULT 1,
        `description` TEXT NULL,
        `image` VARCHAR(255) NULL,
        `status` ENUM('available', 'maintenance') NOT NULL DEFAULT 'available',
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        INDEX `idx_status` (`status`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;
        $sql = "DROP TABLE IF EXISTS rooms;";
        $db->pdo->exec($sql);
    }
}
