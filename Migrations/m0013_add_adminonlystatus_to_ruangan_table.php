<?php

class m0013_add_adminonlystatus_to_ruangan_table {
    public function up() {
        $db = \App\Core\App::$app->db;
        $sql = "ALTER TABLE ruangan MODIFY status_ruangan ENUM('available', 'unavailable', 'adminOnly') NOT NULL DEFAULT 'available';";
        $db->pdo->exec($sql);
    }

    public function down() {
        $db = \App\Core\App::$app->db;
        $sql = "ALTER TABLE ruangan MODIFY status_ruangan ENUM('available', 'unavailable') NOT NULL DEFAULT 'available';";
        $db->pdo->exec($sql);
    }
}