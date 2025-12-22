<?php

class m0004_create_booking_table
{
    public function up()
    {
        $db = \App\Core\App::$app->db;

        $sql = "CREATE TABLE IF NOT EXISTS booking (
            id_booking INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            ruangan_id INT UNSIGNED NOT NULL,
            tanggal_booking DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            tanggal_penggunaan_ruang DATE NOT NULL,
            waktu_mulai TIME NOT NULL,
            waktu_selesai TIME NOT NULL,
            tujuan VARCHAR(255) NOT NULL,
            status ENUM('draft', 'pending', 'verified', 'active', 'completed', 'cancelled', 'expired', 'no_show') DEFAULT 'draft',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_tanggal_penggunaan (tanggal_penggunaan_ruang),
            INDEX idx_status (status),

            CONSTRAINT fk_booking_user 
                FOREIGN KEY (user_id) REFERENCES users(id_user) 
                ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_booking_ruangan 
                FOREIGN KEY (ruangan_id) REFERENCES ruangan(id_ruangan) 
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB 
          DEFAULT CHARSET=utf8mb4 
          COLLATE=utf8mb4_unicode_ci;";

        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;

        $sql = "DROP TABLE IF EXISTS booking;";
        $db->pdo->exec($sql);
    }
}
