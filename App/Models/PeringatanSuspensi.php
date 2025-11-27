<?php

namespace App\Models;

use App\Core\DbModel;

class PeringatanSuspensi extends DbModel
{
    public ?int $id_peringatan = null;
    public string $nama_peringatan = '';

    public static function tableName(): string
    {
        return 'peringatan_suspensi';
    }

    public static function primaryKey(): string
    {
        return 'id_peringatan';
    }

    public function attributes(): array
    {
        return [
            'nama_peringatan',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

    public function peringatanMhs()
    {
        return $this->hasMany(PeringatanMhs::class, 'id_peringatan', 'id_peringatan');
    }

    public function rules(): array
    {
        return [
            'nama_peringatan' => [self::RULE_REQUIRED]
        ];
    }
}