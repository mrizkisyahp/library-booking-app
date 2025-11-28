<?php

class m0014_add_soft_deletes_to_all_table
{
    public function up()
    {
        $db = \App\Core\App::$app->db;
        $db->pdo->exec("ALTER TABLE users ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;");
        $db->pdo->exec("ALTER TABLE booking ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;");
        $db->pdo->exec("ALTER TABLE ruangan ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;");
        $db->pdo->exec("ALTER TABLE role ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;");
        $db->pdo->exec("ALTER TABLE anggota_booking ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;");
        $db->pdo->exec("ALTER TABLE feedback ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;");
        $db->pdo->exec("ALTER TABLE peringatan_mhs ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;");
        $db->pdo->exec("ALTER TABLE peringatan_suspensi ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;");
        $db->pdo->exec("ALTER TABLE suspensi ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;");
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;
        $db->pdo->exec("ALTER TABLE users DROP COLUMN deleted_at;");
        $db->pdo->exec("ALTER TABLE booking DROP COLUMN deleted_at;");
        $db->pdo->exec("ALTER TABLE ruangan DROP COLUMN deleted_at;");
        $db->pdo->exec("ALTER TABLE role DROP COLUMN deleted_at;");
        $db->pdo->exec("ALTER TABLE anggota_booking DROP COLUMN deleted_at;");
        $db->pdo->exec("ALTER TABLE feedback DROP COLUMN deleted_at;");
        $db->pdo->exec("ALTER TABLE peringatan_mhs DROP COLUMN deleted_at;");
        $db->pdo->exec("ALTER TABLE peringatan_suspensi DROP COLUMN deleted_at;");
        $db->pdo->exec("ALTER TABLE suspensi DROP COLUMN deleted_at;");
    }
}