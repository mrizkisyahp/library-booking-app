<?php

/**
 * Migration to create database objects: Indexes, Views, Functions, Trigger
 */
class m0025_create_db_objects
{
    public function up()
    {
        $db = \App\Core\App::$app->db;

        // Index composite untuk query booking berdasarkan tanggal dan status
        $db->pdo->exec("
            CREATE INDEX IF NOT EXISTS idx_booking_date_status 
            ON booking (tanggal_penggunaan_ruang, status)
        ");

        // Index untuk get recent feedbacks
        $db->pdo->exec("
            CREATE INDEX IF NOT EXISTS idx_feedback_created_at 
            ON feedback (created_at)
        ");

        // Index untuk filter invitations by status
        $db->pdo->exec("
            CREATE INDEX IF NOT EXISTS idx_invitation_status 
            ON booking_invitations (status)
        ");

        // View: Booking Summary (gabungan booking + user + ruangan)
        $db->pdo->exec("
            CREATE OR REPLACE VIEW vw_booking_summary AS
            SELECT 
                b.id_booking,
                b.tanggal_penggunaan_ruang,
                b.waktu_mulai,
                b.waktu_selesai,
                b.tujuan,
                b.status,
                b.created_at AS booking_created_at,
                u.id_user,
                u.nama AS nama_pic,
                u.email AS email_pic,
                r.id_ruangan,
                r.nama_ruangan,
                r.kapasitas_max
            FROM booking b
            INNER JOIN users u ON b.user_id = u.id_user
            INNER JOIN ruangan r ON b.ruangan_id = r.id_ruangan
            WHERE b.deleted_at IS NULL
        ");

        // View: Room Statistics (statistik per ruangan)
        $db->pdo->exec("
            CREATE OR REPLACE VIEW vw_room_statistics AS
            SELECT 
                r.id_ruangan,
                r.nama_ruangan,
                r.jenis_ruangan,
                r.status_ruangan,
                COUNT(DISTINCT b.id_booking) AS total_bookings,
                COUNT(DISTINCT CASE WHEN b.status = 'completed' THEN b.id_booking END) AS completed_bookings,
                COALESCE(AVG(f.rating), 0) AS avg_rating,
                COUNT(DISTINCT f.id_feedback) AS total_feedbacks
            FROM ruangan r
            LEFT JOIN booking b ON r.id_ruangan = b.ruangan_id AND b.deleted_at IS NULL
            LEFT JOIN feedback f ON b.id_booking = f.booking_id AND f.deleted_at IS NULL
            WHERE r.deleted_at IS NULL
            GROUP BY r.id_ruangan, r.nama_ruangan, r.jenis_ruangan, r.status_ruangan
        ");

        // View: User Booking Count (jumlah booking per user)
        $db->pdo->exec("
            CREATE OR REPLACE VIEW vw_user_booking_count AS
            SELECT 
                u.id_user,
                u.nama,
                u.email,
                COUNT(DISTINCT b.id_booking) AS total_bookings,
                COUNT(DISTINCT CASE WHEN b.status = 'completed' THEN b.id_booking END) AS completed_bookings,
                COUNT(DISTINCT CASE WHEN b.status IN ('pending', 'verified', 'active') THEN b.id_booking END) AS active_bookings
            FROM users u
            LEFT JOIN booking b ON u.id_user = b.user_id AND b.deleted_at IS NULL
            WHERE u.deleted_at IS NULL
            GROUP BY u.id_user, u.nama, u.email
        ");

        // Function: Get average rating untuk ruangan tertentu
        $db->pdo->exec("
            DROP FUNCTION IF EXISTS fn_get_room_avg_rating
        ");
        $db->pdo->exec("
            CREATE FUNCTION fn_get_room_avg_rating(p_room_id INT UNSIGNED)
            RETURNS DECIMAL(3,2)
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE avg_val DECIMAL(3,2);
                
                SELECT COALESCE(AVG(f.rating), 0.00)
                INTO avg_val
                FROM feedback f
                INNER JOIN booking b ON f.booking_id = b.id_booking
                WHERE b.ruangan_id = p_room_id
                  AND f.deleted_at IS NULL
                  AND b.deleted_at IS NULL;
                
                RETURN avg_val;
            END
        ");

        // Function: Count user active bookings
        $db->pdo->exec("
            DROP FUNCTION IF EXISTS fn_count_user_active_bookings
        ");
        $db->pdo->exec("
            CREATE FUNCTION fn_count_user_active_bookings(p_user_id INT UNSIGNED)
            RETURNS INT
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE booking_count INT;
                
                SELECT COUNT(*)
                INTO booking_count
                FROM booking
                WHERE user_id = p_user_id
                  AND status IN ('pending', 'verified', 'active')
                  AND deleted_at IS NULL;
                
                RETURN booking_count;
            END
        ");

        // Note: MySQL trigger sederhana - hanya untuk demonstrasi
        // Trigger ini akan menyimpan perubahan status ke tabel log jika ada
        // Karena tidak ada tabel log, kita buat trigger yang aman

        $db->pdo->exec("
            DROP TRIGGER IF EXISTS trg_after_booking_status_change
        ");

        // Trigger sederhana: Update updated_at otomatis saat status berubah
        // (Sebenarnya sudah di-handle oleh ON UPDATE CURRENT_TIMESTAMP, tapi ini untuk demo)
        $db->pdo->exec("
            CREATE TRIGGER trg_after_booking_status_change
            AFTER UPDATE ON booking
            FOR EACH ROW
            BEGIN
                -- Trigger ini hanya contoh sederhana
                -- Jika status berubah, kita bisa log atau update field lain
                -- Saat ini hanya placeholder karena tidak ada tabel log
                IF OLD.status <> NEW.status THEN
                    -- Placeholder: Bisa digunakan untuk audit log di masa depan
                    -- INSERT INTO booking_audit_log (booking_id, old_status, new_status) VALUES (NEW.id_booking, OLD.status, NEW.status);
                    SET @last_status_change = NOW();
                END IF;
            END
        ");
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;

        // Drop trigger
        $db->pdo->exec("DROP TRIGGER IF EXISTS trg_after_booking_status_change");

        // Drop functions
        $db->pdo->exec("DROP FUNCTION IF EXISTS fn_count_user_active_bookings");
        $db->pdo->exec("DROP FUNCTION IF EXISTS fn_get_room_avg_rating");

        // Drop views
        $db->pdo->exec("DROP VIEW IF EXISTS vw_user_booking_count");
        $db->pdo->exec("DROP VIEW IF EXISTS vw_room_statistics");
        $db->pdo->exec("DROP VIEW IF EXISTS vw_booking_summary");

        // Drop indexes
        $db->pdo->exec("DROP INDEX idx_invitation_status ON booking_invitations");
        $db->pdo->exec("DROP INDEX idx_feedback_created_at ON feedback");
        $db->pdo->exec("DROP INDEX idx_booking_date_status ON booking");
    }
}
