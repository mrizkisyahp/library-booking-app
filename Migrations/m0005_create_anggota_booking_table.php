<?php

class m0005_create_anggota_booking_table
{
    public function up()
    {
        $db = \App\Core\App::$app->db;

        $sql = "CREATE TABLE IF NOT EXISTS anggota_booking (
            id_anggota INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            booking_id INT UNSIGNED NOT NULL,
            user_id INT UNSIGNED NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            CONSTRAINT fk_anggota_booking 
                FOREIGN KEY (booking_id) REFERENCES booking(id_booking) 
                ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_anggota_user 
                FOREIGN KEY (user_id) REFERENCES users(id_user) 
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB 
          DEFAULT CHARSET=utf8mb4 
          COLLATE=utf8mb4_unicode_ci;";

        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;

        $sql = "DROP TABLE IF EXISTS anggota_booking;";
        $db->pdo->exec($sql);
    }
}
