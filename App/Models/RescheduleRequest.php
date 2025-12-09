<?php

namespace App\Models;

use App\Core\DbModel;

class RescheduleRequest extends DbModel
{
    public ?int $id_request = null;
    public ?int $booking_id = null;
    public ?string $requested_tanggal = null;
    public ?string $requested_waktu_mulai = null;
    public ?string $requested_waktu_selesai = null;
    public ?string $status = null;
    public ?string $reject_reason = null;
    public ?int $requested_by = null;
    public ?int $handled_by = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function tableName(): string
    {
        return 'reschedule_request';
    }

    public static function primaryKey(): string
    {
        return 'id_request';
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id_booking');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id_user');
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by', 'id_user');
    }

    public function attributes(): array
    {
        return [
            'id_request',
            'booking_id',
            'requested_tanggal',
            'requested_waktu_mulai',
            'requested_waktu_selesai',
            'status',
            'reject_reason',
            'requested_by',
            'handled_by',
            'created_at',
            'updated_at',
        ];
    }

    public function rules(): array
    {
        return [];
    }
}