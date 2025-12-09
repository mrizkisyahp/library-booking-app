<?php

use App\Core\Database;

class m0021_create_booking_invitations_table
{
    public function up(): void
    {
        $db = \App\Core\App::$app->db;

        $sql = "CREATE TABLE IF NOT EXISTS booking_invitations (
            id_invitation INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            booking_id INT UNSIGNED NOT NULL,
            invited_user_id INT UNSIGNED NOT NULL,
            invited_by_user_id INT UNSIGNED NULL,
            status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (booking_id) REFERENCES booking(id_booking) ON DELETE CASCADE,
            FOREIGN KEY (invited_user_id) REFERENCES users(id_user) ON DELETE CASCADE,
            FOREIGN KEY (invited_by_user_id) REFERENCES users(id_user) ON DELETE CASCADE,

            UNIQUE KEY unique_invitation (booking_id, invited_user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $db->pdo->exec($sql);
    }

    public function down(): void
    {
        $db = \App\Core\App::$app->db;

        $sql = "DROP TABLE IF EXISTS booking_invitations";
        $db->pdo->exec($sql);
    }
}