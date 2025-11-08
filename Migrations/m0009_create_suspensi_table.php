<?php

class m0009_create_suspensi_table
{
    public function up()
    {
        $db = \App\Core\App::$app->db;

        $sql = "CREATE TABLE IF NOT EXISTS suspensi (
            id_suspensi INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            id_akun INT UNSIGNED NOT NULL,
            tgl_suspensi DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_id_akun (id_akun),
            INDEX idx_tgl_suspensi (tgl_suspensi),

            CONSTRAINT fk_suspensi_akun 
                FOREIGN KEY (id_akun) REFERENCES users(id_user)
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB 
          DEFAULT CHARSET=utf8mb4 
          COLLATE=utf8mb4_unicode_ci;";

        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;

        $sql = 'DROP TABLE IF EXISTS suspensi;';
        $db->pdo->exec($sql);
    }
}
