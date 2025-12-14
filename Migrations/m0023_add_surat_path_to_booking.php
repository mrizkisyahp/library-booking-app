<?php

class m0023_add_surat_path_to_booking
{
    public function up()
    {
        $db = \App\Core\App::$app->db;
        $sql = "ALTER TABLE booking ADD COLUMN surat_path VARCHAR(255) NULL AFTER invite_token;";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;
        $sql = "ALTER TABLE booking DROP COLUMN surat_path;";
        $db->pdo->exec($sql);
    }
}
