<?php

class m0016_requires_special_approval
{
    public function up()
    {
        $db = \App\Core\App::$app->db;
        $sql = "ALTER TABLE ruangan ADD COLUMN requires_special_approval BOOLEAN DEFAULT FALSE;";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;
        $sql = "ALTER TABLE ruangan DROP COLUMN requires_special_approval;";
        $db->pdo->exec($sql);
    }
}