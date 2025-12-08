<?php

namespace App\Models;

use App\Core\DbModel;
use App\Core\App;

class Blocked_Dates extends DbModel
{
    public ?int $id_blocked_date = null;
    public string $tanggal_begin = '';
    public string $tanggal_end = '';
    public ?int $ruangan_id = null;
    public ?string $alasan = null;
    public ?int $created_by = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public ?string $deleted_at = null;

    public static function tableName(): string
    {
        return 'blocked_dates';
    }

    public static function primaryKey(): string
    {
        return 'id_blocked_date';
    }

    public function attributes(): array
    {
        return [
            'id_blocked_date',
            'tanggal_begin',
            'tanggal_end',
            'ruangan_id',
            'alasan',
            'created_by',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
    }

    public function rules(): array
    {
        return [];
    }
}