<?php
class m0018_create_blocked_dates_table
{
    public function up()
    {
        $db = \App\Core\App::$app->db;

        $sql = "CREATE TABLE IF NOT EXISTS blocked_dates (
            id_blocked_date INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            tanggal_begin DATE NOT NULL,
            tanggal_end DATE NOT NULL,
            ruangan_id INT UNSIGNED NULL,
            alasan VARCHAR(255),
            created_by INT UNSIGNED NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_blocked_ruangan
                FOREIGN KEY (ruangan_id) REFERENCES ruangan(id_ruangan)
                ON DELETE CASCADE,
            CONSTRAINT fk_blocked_user
                FOREIGN KEY (created_by) REFERENCES users(id_user)
                ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;
        $sql = "DROP TABLE IF EXISTS blocked_dates;";
        $db->pdo->exec($sql);
    }
}
