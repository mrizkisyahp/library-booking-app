<?php

class m0019_add_deleted_at_to_blocked_dates
{
    public function up()
    {
        $db = \App\Core\App::$app->db;
        $db->pdo->exec("ALTER TABLE blocked_dates ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;");
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;
        $db->pdo->exec("ALTER TABLE blocked_dates DROP COLUMN deleted_at;");
    }
}