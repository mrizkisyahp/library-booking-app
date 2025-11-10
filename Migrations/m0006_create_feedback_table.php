<?php

class m0006_create_feedback_table
{
    public function up()
    {
        $db = \App\Core\App::$app->db;

        $sql = "CREATE TABLE IF NOT EXISTS feedback (
            id_feedback INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            booking_id INT UNSIGNED NOT NULL,
            user_id INT UNSIGNED NOT NULL,
            rating DECIMAL(2,1) check (rating BETWEEN 1.0 and 5.0),
            komentar TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_booking_id (booking_id),
            INDEX idx_user_id (user_id),
            INDEX idx_rating (rating),

            CONSTRAINT fk_feedback_booking 
                FOREIGN KEY (booking_id) REFERENCES booking(id_booking) 
                ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_feedback_user 
                FOREIGN KEY (user_id) REFERENCES users(id_user) 
                ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB 
          DEFAULT CHARSET=utf8mb4 
          COLLATE=utf8mb4_unicode_ci;";

        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;

        $sql = "DROP TABLE IF EXISTS feedback;";
        $db->pdo->exec($sql);
    }
}
