<?php

namespace App\Models;

use App\Core\DbModel;
use App\Core\App;

class Booking extends DbModel
{
    public ?int $id = null;
    public ?int $user_id = null;
    public ?int $room_id = null;
    public string $booking_date = '';
    public string $start_time = '';
    public string $end_time = '';
    public int $participants = 1;
    public string $purpose = '';
    public string $status = 'pending'; // pending, validated, active, completed, cancelled
    public ?string $booking_code = null;
    public ?string $check_in_time = null;
    public ?string $check_out_time = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function tableName(): string
    {
        return 'bookings';
    }

    public static function primaryKey(): string
    {
        return 'id';
    }

    public function rules(): array
    {
        return [
            'room_id' => [self::RULE_REQUIRED, self::RULE_NUMBER],
            'booking_date' => [self::RULE_REQUIRED],
            'start_time' => [self::RULE_REQUIRED],
            'end_time' => [self::RULE_REQUIRED],
            'participants' => [self::RULE_REQUIRED, self::RULE_NUMBER],
            'purpose' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 10]],
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id', 'room_id', 'booking_date', 'start_time', 'end_time',
            'participants', 'purpose', 'status', 'booking_code',
            'check_in_time', 'check_out_time'
        ];
    }   

    // booking rules
    public function validateBooking(): bool
    {
        // Check if room exists
        $room = Room::findOne(['id' => $this->room_id]);
        if (!$room) {
            $this->addError('room_id', 'Room not found.');
            return false;
        }

        // Check if room is available
        if (!$room->isAvailable()) {
            $this->addError('room_id', 'Room is not available for booking.');
            return false;
        }

        // Check participants against room capacity
        if ($this->participants < $room->capacity_min || $this->participants > $room->capacity_max) {
            $this->addError('participants', "Participants must be between {$room->capacity_min} and {$room->capacity_max}.");
            return false;
        }

        // Check if booking date is not in the past
        $bookingDateTime = strtotime($this->booking_date . ' ' . $this->start_time);
        if ($bookingDateTime < time()) {
            $this->addError('booking_date', 'Cannot book in the past.');
            return false;
        }

        // Check if end time is after start time
        if ($this->end_time <= $this->start_time) {
            $this->addError('end_time', 'End time must be after start time.');
            return false;
        }

        // Check for time slot conflicts
        if ($this->hasTimeConflict()) {
            $this->addError('start_time', 'This time slot is already booked.');
            return false;
        }

        // Check if user already has an active booking 
        if ($this->userHasActiveBooking()) {
            $this->addError('room_id', 'You already have an active booking. Please complete or cancel it first.');
            return false;
        }

        return true;
    }

    // apakah sudah ada booking di jam itu
    private function hasTimeConflict(): bool
    {
        $sql = "SELECT COUNT(*) as count FROM bookings 
                WHERE room_id = :room_id 
                AND booking_date = :booking_date 
                AND status NOT IN ('cancelled', 'completed')
                AND (
                    (start_time < :end_time AND end_time > :start_time)
                )";
        
        if ($this->id) {
            $sql .= " AND id != :id";
        }

        $stmt = App::$app->db->prepare($sql);
        $stmt->bindValue(':room_id', $this->room_id);
        $stmt->bindValue(':booking_date', $this->booking_date);
        $stmt->bindValue(':start_time', $this->start_time);
        $stmt->bindValue(':end_time', $this->end_time);
        
        if ($this->id) {
            $stmt->bindValue(':id', $this->id);
        }

        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    // apakah user dah punya booking room?
    private function userHasActiveBooking(): bool
    {
        $sql = "SELECT COUNT(*) as count FROM bookings 
                WHERE user_id = :user_id 
                AND status IN ('pending', 'validated', 'active')";
        
        if ($this->id) {
            $sql .= " AND id != :id";
        }

        $stmt = App::$app->db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id);
        
        if ($this->id) {
            $stmt->bindValue(':id', $this->id);
        }

        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    // generate bookingcode
    public function generateBookingCode(): string
    {
        return 'BK' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    // save booking code
    public function save(): bool
    {
        $this->user_id = App::$app->user->id;
        $this->status = 'pending';
        $this->booking_code = $this->generateBookingCode();
        
        return parent::save();
    }

    // get user booking
    public static function getUserBookings(int $userId): array
    {
        $sql = "SELECT b.*, r.title as room_title, r.capacity_min, r.capacity_max
                FROM bookings b
                INNER JOIN rooms r ON b.room_id = r.id
                WHERE b.user_id = :user_id
                ORDER BY b.booking_date DESC, b.start_time DESC";
        
        $stmt = App::$app->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // get user booking dengan id (for admin)
    public static function getAllBookingsWithDetails(): array
    {
        $sql = "SELECT b.*, 
                       u.nama as user_name, u.email as user_email, u.role as user_role,
                       r.title as room_title
                FROM bookings b
                INNER JOIN users u ON b.user_id = u.id
                INNER JOIN rooms r ON b.room_id = r.id
                ORDER BY b.booking_date DESC, b.start_time DESC";
        $stmt = App::$app->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // check in booking
    public function checkIn(): bool
    {
        if ($this->status !== 'validated') {
            return false;
        }

        $stmt = App::$app->db->prepare(
            "UPDATE bookings SET status = 'active', check_in_time = NOW() WHERE id = :id"
        );
        $stmt->bindValue(':id', $this->id);
        
        if ($stmt->execute()) {
            $this->status = 'active';
            $this->check_in_time = date('Y-m-d H:i:s');
            return true;
        }

        return false;
    }

    // check out booking
    public function checkOut(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $stmt = App::$app->db->prepare(
            "UPDATE bookings SET check_out_time = NOW() WHERE id = :id"
        );
        $stmt->bindValue(':id', $this->id);
        
        if ($stmt->execute()) {
            $this->check_out_time = date('Y-m-d H:i:s');
            return true;
        }

        return false;
    }

    // auto cancel kalau expired
    public static function autoCancelExpiredBookings(): int
    {
        // Get bookings that should have started but haven't been checked in
        $sql = "UPDATE bookings 
                SET status = 'cancelled' 
                WHERE status = 'validated' 
                AND CONCAT(booking_date, ' ', start_time) < DATE_SUB(NOW(), INTERVAL 10 MINUTE)
                AND check_in_time IS NULL";
        
        $stmt = App::$app->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    // get booking yang perlu reminder
    public static function getBookingsNeedingReminder(): array
    {
        $sql = "SELECT b.*, u.nama, u.email
                FROM bookings b
                INNER JOIN users u ON b.user_id = u.id
                WHERE b.status = 'validated'
                AND CONCAT(b.booking_date, ' ', b.start_time) BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 10 MINUTE)
                AND b.check_in_time IS NULL";
        
        $stmt = App::$app->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
