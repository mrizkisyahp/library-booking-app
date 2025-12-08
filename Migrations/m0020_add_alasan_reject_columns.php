<?php

class m0020_add_alasan_reject_columns
{
    public function up()
    {
        $db = \App\Core\App::$app->db;

        // Add alasan_reject to booking table
        $sql1 = "ALTER TABLE booking ADD COLUMN alasan_reject TEXT NULL AFTER status;";

        // Add alasan_reject to users table
        $sql2 = "ALTER TABLE users ADD COLUMN alasan_reject TEXT NULL AFTER status;";

        $db->pdo->exec($sql1);
        $db->pdo->exec($sql2);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;

        $sql1 = "ALTER TABLE booking DROP COLUMN alasan_reject;";
        $sql2 = "ALTER TABLE users DROP COLUMN alasan_reject;";

        $db->pdo->exec($sql1);
        $db->pdo->exec($sql2);
    }
}
