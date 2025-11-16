<?php

namespace App\Models;

use App\Core\DbModel;
use App\Core\App;

class Booking extends DbModel {
    public ?int $id_booking = null;
    public ?int $user_id = null;
    public ?int $ruangan_id = null;
    public string $tanggal_booking = '';
    public string $tanggal_penggunaan_ruang = '';
    public string $waktu_mulai = '';
    public string $waktu_selesai = '';
    public string $tujuan = '';
    public string $status = 'draft'; // draft, pending, verified, active, completed, cancelled, expired, no_show
    public ?string $checkin_code = null;
    public ?string $invite_token = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function tableName(): string {
        return 'booking';
    }

    public static function primaryKey(): string {
        return 'id_booking';
    }

    public function attributes(): array {
        return [
            'user_id',
            'ruangan_id',
            'tanggal_booking',
            'tanggal_penggunaan_ruang',
            'waktu_mulai',
            'waktu_selesai',
            'tujuan',
            'status',
            'checkin_code',
            'invite_token',
        ];
    }

    public function rules(): array {
        return [];
    }

    public function getMembers(): array
    {
        $stmt = App::$app->db->prepare("
            SELECT 
                ab.id_anggota,
                ab.booking_id,
                u.id_user,
                u.nama,
                u.email,
                ab.created_at AS joined_at,
                0 AS is_owner
            FROM anggota_booking ab
            JOIN users u ON u.id_user = ab.user_id
            WHERE ab.booking_id = :id
            ORDER BY u.nama ASC
        ");
        
        $stmt->bindValue(':id', $this->id_booking, \PDO::PARAM_INT);
        $stmt->execute();

        $members = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $ownerIncluded = false;

        foreach ($members as $m) {
            if ((int)$m['id_user'] === (int)$this->user_id) {
                $ownerIncluded = true;
                break;
            }
        }

        if (!$ownerIncluded) {
            $pic = User::findOne(['id_user' => $this->user_id]);
            if ($pic) {
                $members[] = [
                    'id_anggota' => null,
                    'booking_id' => $this->id_booking,
                    'id_user' => $pic->id_user,
                    'nama' => $pic->nama,
                    'email' => $pic->email,
                    'joined_at' => $pic->created_at,
                    'is_owner' => 1,
                ];
            }
        }

        usort($members, function ($a, $b) {
            if ($a['is_owner'] === $b['is_owner']) {
                return strcmp($a['nama'], $b['nama']); 
            }
            return $a['is_owner'] ? -1 : 1; 
        });

        return $members;
    }

}
