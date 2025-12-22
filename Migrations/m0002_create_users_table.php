<?php

class m0002_create_users_table
{
    public function up()
    {
        $db = \App\Core\App::$app->db;
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id_user INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            id_role INT UNSIGNED NOT NULL,
            nama VARCHAR(100) NOT NULL,
            nim CHAR(10) NULL,
            nip CHAR(18) NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            kubaca_img VARCHAR(255) NULL,
            peringatan TINYINT UNSIGNED NOT NULL DEFAULT 0,
            status ENUM('active', 'pending kubaca', 'rejected', 'suspended') NOT NULL DEFAULT 'pending kubaca',
            jurusan VARCHAR(100) NULL,
            nomor_hp VARCHAR(20) NULL,
            suspensi_terakhir DATE NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_nim (nim),
            INDEX idx_nip (nip),
            INDEX idx_status (status),
            INDEX idx_jurusan (jurusan),
            INDEX idx_nomor_hp (nomor_hp),

            CONSTRAINT fk_users_role
                FOREIGN KEY (id_role) REFERENCES role(id_role)
                ON DELETE RESTRICT
                ON UPDATE CASCADE
        ) ENGINE=InnoDB 
          DEFAULT CHARSET=utf8mb4 
          COLLATE=utf8mb4_unicode_ci;";
          
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;
        $sql = "DROP TABLE IF EXISTS users;";
        $db->pdo->exec($sql);
    }
}
