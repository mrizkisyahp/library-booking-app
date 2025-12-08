<?php

namespace App\Models;

use App\Core\DbModel;

class PeringatanMhs extends DbModel
{
    public ?int $id_peringatan_mhs = null;
    public ?int $id_akun = null;
    public ?int $id_peringatan = null;
    public string $tgl_peringatan = '';
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public ?string $deleted_at = null;

    public static function tableName(): string
    {
        return 'peringatan_mhs';
    }

    public static function primaryKey(): string
    {
        return 'id_peringatan_mhs';
    }

    public function attributes(): array
    {
        return [
            'id_peringatan_mhs',
            'id_akun',
            'id_peringatan',
            'tgl_peringatan',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_akun', 'id_user');
    }

    public function peringatan()
    {
        return $this->belongsTo(PeringatanSuspensi::class, 'id_peringatan', 'id_peringatan');
    }

    public function rules(): array
    {
        return [];
    }
}