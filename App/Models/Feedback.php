<?php

namespace App\Models;

use App\Core\DbModel;
use App\Core\App;

class Feedback extends DbModel {
    public ?int $id_feedback = null;
    public ?int $booking_id = null;
    public ?int $user_id = null;
    public ?int $rating = null;
    public ?string $komentar = '';
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function tableName(): string {
        return 'feedback';
    }

    public static function primaryKey(): string {
        return 'id_feedback';
    }

    public function attributes(): array {
        return [
            'booking_id',
            'user_id',
            'rating',
            'komentar'
        ];
    }

    public function rules(): array {
        return [];
    }
}