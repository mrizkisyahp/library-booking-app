<?php

class m0011_add_invite_token_to_booking
{
    public function up()
    {
        $db = \App\Core\App::$app->db;
        $db->pdo->exec("ALTER TABLE booking ADD COLUMN invite_token CHAR(36) NULL AFTER checkin_code;");
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;
        $db->pdo->exec("ALTER TABLE booking DROP COLUMN invite_token;");
    }
}
