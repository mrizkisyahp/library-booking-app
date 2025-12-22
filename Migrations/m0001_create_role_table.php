<?php

class m0001_create_role_table
{
    public function up()
    {
        $db = \App\Core\App::$app->db;

        $sql = "CREATE TABLE IF NOT EXISTS role (
            id_role INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nama_role VARCHAR(50) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        ) ENGINE=InnoDB 
          DEFAULT CHARSET=utf8mb4 
          COLLATE=utf8mb4_unicode_ci;";

        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;

        $sql = 'DROP TABLE IF EXISTS role;';
        $db->pdo->exec($sql);
    }
}
