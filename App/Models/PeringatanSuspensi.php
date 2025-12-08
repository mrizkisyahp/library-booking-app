<?php

namespace App\Models;

use App\Core\DbModel;

class PeringatanSuspensi extends DbModel
{
    public ?int $id_peringatan = null;
    public string $nama_peringatan = '';
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public ?string $deleted_at = null;

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
            'id_peringatan',
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
        return [];
    }
}