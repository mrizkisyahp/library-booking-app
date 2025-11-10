<?php

class m0010_add_checkin_code_to_booking
{
    public function up()
    {
        $db = \App\Core\App::$app->db;
        $db->pdo->exec("ALTER TABLE booking ADD COLUMN checkin_code CHAR(8) NULL AFTER status;");
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;
        $db->pdo->exec("ALTER TABLE booking DROP COLUMN checkin_code;");
    }
}
