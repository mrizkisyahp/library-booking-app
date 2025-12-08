<?php

class m0017_add_has_been_rescheduled_column
{
    public function up()
    {
        $db = \App\Core\App::$app->db;
        $sql = "ALTER TABLE booking ADD COLUMN has_been_rescheduled BOOLEAN DEFAULT FALSE;";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;
        $sql = "ALTER TABLE booking DROP COLUMN has_been_rescheduled;";
        $db->pdo->exec($sql);
    }
}