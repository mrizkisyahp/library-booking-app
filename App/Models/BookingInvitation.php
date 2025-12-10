<?php

namespace App\Models;

use App\Core\DbModel;

class BookingInvitation extends DbModel
{
    public int $id_invitation = 0;
    public int $booking_id = 0;
    public int $invited_user_id = 0;
    public ?int $invited_by_user_id = null;
    public string $status = 'pending';
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function tableName(): string
    {
        return 'booking_invitations';
    }

    public static function primaryKey(): string
    {
        return 'id_invitation';
    }

    public function attributes(): array
    {
        return [
            'booking_id',
            'invited_user_id',
            'invited_by_user_id',
            'status',
        ];
    }

    public function rules(): array
    {
        return [];
    }
}