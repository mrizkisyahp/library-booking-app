<?php

class m0015_add_password_reset_token
{
    public function up()
    {
        $db = \App\Core\App::$app->db;
        $sql = "ALTER TABLE users ADD COLUMN password_reset_token VARCHAR(64) DEFAULT NULL;";
        $sql2 = "ALTER TABLE users ADD COLUMN password_reset_expires DATETIME DEFAULT NULL;";
        $db->pdo->exec($sql);
        $db->pdo->exec($sql2);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;
        $sql = "ALTER TABLE users DROP COLUMN password_reset_token;";
        $sql2 = "ALTER TABLE users DROP COLUMN password_reset_expires;";
        $db->pdo->exec($sql);
        $db->pdo->exec($sql2);
    }
}