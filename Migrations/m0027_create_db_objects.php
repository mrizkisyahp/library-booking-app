<?php

/**
 * Migration to create database views, functions, procedures, and triggers
 */
class m0027_create_db_objects
{
    public function up()
    {
        $db = \App\Core\App::$app->db;

        // ===== CREATE VIEWS =====

        // View 1: vw_booking_detail
        $db->pdo->exec("
            CREATE VIEW vw_booking_detail AS
            SELECT
                b.id_booking,
                b.tanggal_booking,
                b.tanggal_penggunaan_ruang,
                b.waktu_mulai,
                b.waktu_selesai,
                b.tujuan,
                b.status,
                b.checkin_code,
                b.invite_token,
                b.surat_path,
                b.has_been_rescheduled,
                r.nama_ruangan,
                r.deskripsi_ruangan,
                r.kapasitas_min,
                r.kapasitas_max,
                r.jenis_ruangan,
                u.nama AS pic_nama,
                u.email AS pic_email,
                u.nim,
                u.nip
            FROM booking b
            JOIN ruangan r ON r.id_ruangan = b.ruangan_id
            JOIN users u ON u.id_user = b.user_id
            WHERE b.deleted_at IS NULL
        ");

        // View 2: vw_booking_members
        $db->pdo->exec("
            CREATE VIEW vw_booking_members AS
            SELECT
                b.id_booking,
                b.tanggal_penggunaan_ruang,
                r.nama_ruangan,
                pic.nama AS pic_nama,
                member.nama AS member_nama,
                member.nim AS member_nim,
                member.email AS member_email
            FROM booking b
            JOIN ruangan r ON r.id_ruangan = b.ruangan_id
            JOIN users pic ON pic.id_user = b.user_id
            JOIN anggota_booking ab ON ab.booking_id = b.id_booking
            JOIN users member ON member.id_user = ab.user_id
            WHERE b.deleted_at IS NULL AND ab.deleted_at IS NULL
        ");

        // View 3: vw_booking_aktif
        $db->pdo->exec("
            CREATE VIEW vw_booking_aktif AS
            SELECT 
                b.id_booking,
                u.nama AS nama_peminjam,
                u.nim,
                u.nip,
                r.nama_ruangan,
                b.tanggal_penggunaan_ruang,
                b.waktu_mulai,
                b.waktu_selesai,
                b.tujuan,
                b.status
            FROM booking b
            JOIN users u ON b.user_id = u.id_user
            JOIN ruangan r ON b.ruangan_id = r.id_ruangan
            WHERE b.status IN ('pending', 'verified', 'active')
            AND b.deleted_at IS NULL
        ");

        // ===== CREATE FUNCTIONS =====

        // Function 1: fn_average_room_rating
        $db->pdo->exec("
            CREATE FUNCTION fn_average_room_rating(p_ruangan_id INT)
            RETURNS DECIMAL(2,1)
            DETERMINISTIC
            BEGIN
                DECLARE avg_rating DECIMAL(2,1);
                
                SELECT AVG(f.rating) INTO avg_rating
                FROM feedback f
                JOIN booking b ON f.booking_id = b.id_booking
                WHERE b.ruangan_id = p_ruangan_id
                AND f.deleted_at IS NULL;
                
                RETURN IFNULL(avg_rating, 0);
            END
        ");

        // Function 2: fn_get_booking_runtime_status
        $db->pdo->exec("
            CREATE FUNCTION fn_get_booking_runtime_status(
                p_status VARCHAR(20),
                p_tanggal DATE,
                p_waktu_mulai TIME,
                p_waktu_selesai TIME
            )
            RETURNS VARCHAR(20)
            DETERMINISTIC
            BEGIN
                IF p_status <> 'verified' THEN
                    RETURN p_status;
                END IF;

                IF NOW() < TIMESTAMP(p_tanggal, p_waktu_mulai) THEN
                    RETURN 'verified';
                ELSEIF NOW() BETWEEN 
                    TIMESTAMP(p_tanggal, p_waktu_mulai) AND 
                    TIMESTAMP(p_tanggal, p_waktu_selesai) THEN
                    RETURN 'active';
                ELSE
                    RETURN 'completed';
                END IF;
            END
        ");

        // Function 3: fn_is_room_available
        $db->pdo->exec("
            CREATE FUNCTION fn_is_room_available(
                p_ruangan_id INT,
                p_tanggal DATE,
                p_waktu_mulai TIME,
                p_waktu_selesai TIME
            )
            RETURNS BOOLEAN
            DETERMINISTIC
            BEGIN
                DECLARE conflict INT;

                SELECT COUNT(*) INTO conflict
                FROM booking
                WHERE ruangan_id = p_ruangan_id
                  AND tanggal_penggunaan_ruang = p_tanggal
                  AND status IN ('verified', 'active')
                  AND deleted_at IS NULL
                  AND (
                        p_waktu_mulai < waktu_selesai
                    AND p_waktu_selesai > waktu_mulai
                  );

                RETURN conflict = 0;
            END
        ");

        // ===== CREATE PROCEDURES =====

        // Procedure: sp_apply_warning
        $db->pdo->exec("
            CREATE PROCEDURE sp_apply_warning(
                IN p_user_id INT,
                IN p_peringatan_id INT
            )
            BEGIN
                -- Insert riwayat peringatan
                INSERT INTO peringatan_mhs (id_akun, id_peringatan, tgl_peringatan)
                VALUES (p_user_id, p_peringatan_id, CURDATE());
                
                -- Update jumlah peringatan user
                UPDATE users 
                SET peringatan = peringatan + 1
                WHERE id_user = p_user_id;
                
                -- Auto-suspensi jika peringatan >= 3
                IF (SELECT peringatan FROM users WHERE id_user = p_user_id) >= 3 THEN
                    UPDATE users 
                    SET status = 'suspended',
                        suspensi_terakhir = CURDATE()
                    WHERE id_user = p_user_id;
                    
                    INSERT INTO suspensi (id_akun, tgl_suspensi)
                    VALUES (p_user_id, CURDATE());
                END IF;
            END
        ");

        // ===== CREATE TRIGGERS =====

        // Trigger 1: trg_booking_after_insert
        $db->pdo->exec("
            CREATE TRIGGER trg_booking_after_insert
            AFTER INSERT ON booking
            FOR EACH ROW
            BEGIN
                INSERT INTO booking_logs (
                    booking_id,
                    action,
                    old_status,
                    new_status,
                    changed_by,
                    alasan,
                    change_time
                ) VALUES (
                    NEW.id_booking,
                    'INSERT',
                    NULL,
                    NEW.status,
                    NEW.user_id,
                    'Booking baru dibuat',
                    NOW()
                );
            END
        ");

        // Trigger 2: trg_booking_after_update
        $db->pdo->exec("
            CREATE TRIGGER trg_booking_after_update
            AFTER UPDATE ON booking
            FOR EACH ROW
            BEGIN
                IF OLD.status <> NEW.status THEN
                    INSERT INTO booking_logs (
                        booking_id,
                        action,
                        old_status,
                        new_status,
                        changed_by,
                        alasan,
                        change_time
                    ) VALUES (
                        NEW.id_booking,
                        'UPDATE',
                        OLD.status,
                        NEW.status,
                        NEW.user_id,
                        CASE 
                            WHEN NEW.status = 'cancelled' THEN 'Booking dibatalkan'
                            WHEN NEW.status = 'verified' THEN 'Booking diverifikasi admin'
                            WHEN NEW.status = 'active' THEN 'Booking sedang berlangsung'
                            WHEN NEW.status = 'completed' THEN 'Booking selesai'
                            WHEN NEW.status = 'no_show' THEN 'User tidak hadir'
                            ELSE 'Status diubah'
                        END,
                        NOW()
                    );
                END IF;
            END
        ");

        // Trigger 3: trg_booking_after_delete
        $db->pdo->exec("
            CREATE TRIGGER trg_booking_after_delete
            AFTER DELETE ON booking
            FOR EACH ROW
            BEGIN
                INSERT INTO booking_logs (
                    booking_id,
                    action,
                    old_status,
                    new_status,
                    changed_by,
                    alasan,
                    change_time
                ) VALUES (
                    OLD.id_booking,
                    'DELETE',
                    OLD.status,
                    NULL,
                    OLD.user_id,
                    'Booking dihapus dari sistem',
                    NOW()
                );
            END
        ");

        // Trigger 4: trg_user_after_insert
        $db->pdo->exec("
            CREATE TRIGGER trg_user_after_insert
            AFTER INSERT ON users
            FOR EACH ROW
            BEGIN
                INSERT INTO user_logs (
                    user_id,
                    action,
                    details,
                    alasan,
                    change_time
                ) VALUES (
                    NEW.id_user,
                    'INSERT',
                    CONCAT('User baru: ', NEW.nama, ' (', NEW.email, ')'),
                    'Pendaftaran akun baru',
                    NOW()
                );
            END
        ");

        // Trigger 5: trg_user_after_update
        $db->pdo->exec("
            CREATE TRIGGER trg_user_after_update
            AFTER UPDATE ON users
            FOR EACH ROW
            BEGIN
                IF OLD.status <> NEW.status THEN
                    INSERT INTO user_logs (
                        user_id,
                        action,
                        details,
                        alasan,
                        change_time
                    ) VALUES (
                        NEW.id_user,
                        'UPDATE',
                        CONCAT('Status berubah: ', OLD.status, ' -> ', NEW.status),
                        CASE 
                            WHEN NEW.status = 'active' THEN 'Akun diaktifkan'
                            WHEN NEW.status = 'suspended' THEN 'Akun disuspensi'
                            WHEN NEW.status = 'rejected' THEN 'Pendaftaran ditolak'
                            WHEN NEW.status = 'nonaktif' THEN 'Akun dinonaktifkan'
                            ELSE 'Status diubah'
                        END,
                        NOW()
                    );
                END IF;
                
                IF OLD.peringatan <> NEW.peringatan THEN
                    INSERT INTO user_logs (
                        user_id,
                        action,
                        details,
                        alasan,
                        change_time
                    ) VALUES (
                        NEW.id_user,
                        'UPDATE',
                        CONCAT('Peringatan: ', OLD.peringatan, ' -> ', NEW.peringatan),
                        'Jumlah peringatan diperbarui',
                        NOW()
                    );
                END IF;
            END
        ");

        // Trigger 6: trg_user_after_delete
        $db->pdo->exec("
            CREATE TRIGGER trg_user_after_delete
            AFTER DELETE ON users
            FOR EACH ROW
            BEGIN
                INSERT INTO user_logs (
                    user_id,
                    action,
                    details,
                    alasan,
                    change_time
                ) VALUES (
                    OLD.id_user,
                    'DELETE',
                    CONCAT('User dihapus: ', OLD.nama, ' (', OLD.email, ')'),
                    'Akun dihapus dari sistem',
                    NOW()
                );
            END
        ");
    }

    public function down()
    {
        $db = \App\Core\App::$app->db;

        // Drop triggers
        $db->pdo->exec("DROP TRIGGER IF EXISTS trg_user_after_delete");
        $db->pdo->exec("DROP TRIGGER IF EXISTS trg_user_after_update");
        $db->pdo->exec("DROP TRIGGER IF EXISTS trg_user_after_insert");
        $db->pdo->exec("DROP TRIGGER IF EXISTS trg_booking_after_delete");
        $db->pdo->exec("DROP TRIGGER IF EXISTS trg_booking_after_update");
        $db->pdo->exec("DROP TRIGGER IF EXISTS trg_booking_after_insert");

        // Drop procedure
        $db->pdo->exec("DROP PROCEDURE IF EXISTS sp_apply_warning");

        // Drop functions
        $db->pdo->exec("DROP FUNCTION IF EXISTS fn_is_room_available");
        $db->pdo->exec("DROP FUNCTION IF EXISTS fn_get_booking_runtime_status");
        $db->pdo->exec("DROP FUNCTION IF EXISTS fn_average_room_rating");

        // Drop views
        $db->pdo->exec("DROP VIEW IF EXISTS vw_booking_aktif");
        $db->pdo->exec("DROP VIEW IF EXISTS vw_booking_members");
        $db->pdo->exec("DROP VIEW IF EXISTS vw_booking_detail");
    }
}
