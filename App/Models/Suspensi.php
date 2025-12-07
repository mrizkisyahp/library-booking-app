<?php

namespace App\Models;

use App\Core\DbModel;

class Suspensi extends DbModel
{
    public ?int $id_suspensi = null;
    public ?int $id_akun = null;
    public string $tgl_suspensi = '';
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public ?string $deleted_at = null;

    public static function tableName(): string
    {
        return 'suspensi';
    }

    public static function primaryKey(): string
    {
        return 'id_suspensi';
    }

    public function attributes(): array
    {
        return [
            'id_suspensi',
            'id_akun',
            'tgl_suspensi',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_akun', 'id_user');
    }

    public function rules(): array
    {
        return [];
    }
}