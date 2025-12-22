<?php

class m0026_remove_alasan_reject_from_booking_and_users
{
    public function up()
    {
        $db = \App\Core\App::$app->db;

        // Add alasan_reject to booking table
        $sql1 = "ALTER TABLE booking DROP COLUMN alasan_reject;";

        // Add alasan_reject to users table
        $sql2 = "ALTER TABLE users DROP COLUMN alasan_reject;";

        $db->pdo->exec($sql1);
        $db->pdo->exec($sql2);
    }

    public function down()
    {

    }
}
