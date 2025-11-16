<?php

class m0012_add_masa_aktif_role_status_nonaktif_to_users_table {
    public function up() {
        $db = \App\Core\App::$app->db;
        $sql1 = "ALTER TABLE users ADD column masa_aktif DATE NULL after suspensi_terakhir;";

        $sql2 = "ALTER TABLE users modify status ENUM('active', 'pending verification', 'pending kubaca', 'rejected', 'suspended', 'nonaktif') NOT NULL DEFAULT 'pending verification';";

        $db->pdo->exec($sql1);
        $db->pdo->exec($sql2);
    }

    public function down() {
        $db = \App\Core\App::$app->db;
        $sql1 = "ALTER TABLE users 
                 DROP COLUMN masa_aktif;";
        $sql2 = "ALTER TABLE users 
                 MODIFY status ENUM('active', 'pending verification', 'pending kubaca', 'rejected', 'suspended') 
                 NOT NULL DEFAULT 'pending verification';";
        $db->pdo->exec($sql1);
        $db->pdo->exec($sql2);
    }
}