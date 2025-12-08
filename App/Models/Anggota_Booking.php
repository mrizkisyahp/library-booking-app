<?php

namespace App\Models;

use App\Core\DbModel;
use App\Core\App;

class Anggota_Booking extends DbModel
{
    public ?int $id_anggota = null;
    public ?int $booking_id = null;
    public ?int $user_id = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public ?string $deleted_at = null;

    public static function tableName(): string
    {
        return 'anggota_booking';
    }

    public static function primaryKey(): string
    {
        return 'id_anggota';
    }

    public function attributes(): array
    {
        return [
            'id_anggota',
            'booking_id',
            'user_id',
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