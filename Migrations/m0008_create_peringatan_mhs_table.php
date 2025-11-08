<?php

class m0008_create_peringatan_mhs_table
{
    public function up()
    {
        $db = \App\Core\App::$app->db;

        $sql = "CREATE TABLE IF NOT EXISTS peringatan_mhs (
            id_peringatan_mhs INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            id_akun INT UNSIGNED NOT NULL,
            id_peringatan INT UNSIGNED NOT NULL,
            tgl_peringatan DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_id_akun (id_akun),
            INDEX idx_id_peringatan (id_peringatan),

            CONSTRAINT fk_peringatan_mhs_akun 
                FOREIGN KEY (id_akun) REFERENCES users(id_user)
                ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_peringatan_mhs_peringatan 
                FOREIGN KEY (id_peringatan) REFERENCES peringatan_suspensi(id_peringatan)
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB 
          DEFAULT CHARSET=utf8mb4 
          COLLATE=utf8mb4_unicode_ci;";

        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;

        $sql = 'DROP TABLE IF EXISTS peringatan_mhs;';
        $db->pdo->exec($sql);
    }
}
