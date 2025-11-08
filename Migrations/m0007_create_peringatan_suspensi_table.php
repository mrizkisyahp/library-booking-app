<?php

class m0007_create_peringatan_suspensi_table
{
    public function up()
    {
        $db = \App\Core\App::$app->db;

        $sql = "CREATE TABLE IF NOT EXISTS peringatan_suspensi (
            id_peringatan INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nama_peringatan VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_nama_peringatan (nama_peringatan)
        ) ENGINE=InnoDB 
          DEFAULT CHARSET=utf8mb4 
          COLLATE=utf8mb4_unicode_ci;";

        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;

        $sql = 'DROP TABLE IF EXISTS peringatan_suspensi;';
        $db->pdo->exec($sql);
    }
}
