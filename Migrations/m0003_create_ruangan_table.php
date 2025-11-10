<?php

class m0003_create_ruangan_table
{
    public function up()
    {
        $db = \App\Core\App::$app->db;

        $sql = "CREATE TABLE IF NOT EXISTS ruangan (
            id_ruangan INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nama_ruangan VARCHAR(100) NOT NULL,
            kapasitas_min INT UNSIGNED NOT NULL,
            kapasitas_max INT UNSIGNED NOT NULL,
            jenis_ruangan VARCHAR(50) NOT NULL, 
            deskripsi_ruangan TEXT NOT NULL,
            status_ruangan ENUM('available', 'unavailable') NOT NULL DEFAULT 'available',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_nama_ruangan (nama_ruangan),
            INDEX idx_jenis_ruangan (jenis_ruangan)
        ) ENGINE=InnoDB 
          DEFAULT CHARSET=utf8mb4 
          COLLATE=utf8mb4_unicode_ci;";

        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;

        $sql = "DROP TABLE IF EXISTS ruangan;";
        $db->pdo->exec($sql);
    }
}
