<?php

namespace App\Models;

use App\Core\DbModel;

class PeringatanMhs extends DbModel
{
    public ?int $id_peringatan_mhs = null;
    public ?int $id_akun = null;
    public ?int $id_peringatan = null;
    public string $tgl_peringatan = '';

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
        return [
            'id_akun' => [self::RULE_REQUIRED],
            'id_peringatan' => [self::RULE_REQUIRED],
            'tgl_peringatan' => [self::RULE_REQUIRED],
        ];
    }
}