<?php

/**
 * Migration to create booking_logs and user_logs tables
 */
class m0025_create_log_tables
{
    public function up()
    {
        $db = \App\Core\App::$app->db;

        // Create booking_logs table
        $db->pdo->exec("
            CREATE TABLE IF NOT EXISTS booking_logs (
                id_log INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                booking_id INT(10) UNSIGNED NOT NULL,
                action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
                old_status VARCHAR(20) DEFAULT NULL,
                new_status VARCHAR(20) DEFAULT NULL,
                changed_by INT(10) UNSIGNED DEFAULT NULL,
                alasan TEXT DEFAULT NULL,
                change_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id_log),
                CONSTRAINT fk_booking_log_booking
                    FOREIGN KEY (booking_id) REFERENCES booking(id_booking)
                    ON DELETE CASCADE,
                CONSTRAINT fk_booking_log_user
                    FOREIGN KEY (changed_by) REFERENCES users(id_user)
                    ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // Create user_logs table
        $db->pdo->exec("
            CREATE TABLE IF NOT EXISTS user_logs (
                id_log INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id INT(10) UNSIGNED NOT NULL,
                action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
                details TEXT DEFAULT NULL,
                alasan TEXT DEFAULT NULL,
                change_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id_log),
                CONSTRAINT fk_user_log_user
                    FOREIGN KEY (user_id) REFERENCES users(id_user)
                    ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;

        $db->pdo->exec("DROP TABLE IF EXISTS booking_logs");
        $db->pdo->exec("DROP TABLE IF EXISTS user_logs");
    }
}
