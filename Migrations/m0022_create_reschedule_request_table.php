<?php

use App\Core\Database;

class m0022_create_reschedule_request_table
{
    public function up(): void
    {
        $db = \App\Core\App::$app->db;

        $sql = "CREATE TABLE IF NOT EXISTS reschedule_request (
            id_request INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            booking_id INT UNSIGNED NOT NULL,
            requested_tanggal DATE NOT NULL,
            requested_waktu_mulai TIME NOT NULL,
            requested_waktu_selesai TIME NOT NULL,
            status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
            reject_reason VARCHAR(255) NULL,
            requested_by INT UNSIGNED NOT NULL,
            handled_by INT UNSIGNED NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (booking_id) REFERENCES booking(id_booking) ON DELETE CASCADE,
            FOREIGN KEY (requested_by) REFERENCES users(id_user),
            FOREIGN KEY (handled_by) REFERENCES users(id_user)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $db->pdo->exec($sql);
    }

    public function down(): void
    {
        $db = \App\Core\App::$app->db;

        $sql = "DROP TABLE IF EXISTS reschedule_request";
        $db->pdo->exec($sql);
    }
}