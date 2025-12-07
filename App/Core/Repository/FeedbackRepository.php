<?php

namespace App\Core\Repository;

use App\Core\QueryBuilder;
use App\Core\Database;
use App\Models\Feedback;

class FeedbackRepository
{
    public function __construct(private Database $database)
    {
    }

    public function findById(int $id): ?Feedback
    {
        return Feedback::Query()->where('id_feedback', $id)->first();
    }

    public function findByBookingId(int $bookingId): ?Feedback
    {
        return Feedback::Query()
            ->where('booking_id', $bookingId)
            ->first();
    }

    public function create(array $data): Feedback
    {
        $feedback = new Feedback();

        foreach ($data as $key => $value) {
            if (property_exists($feedback, $key)) {
                $feedback->{$key} = $value;
            }
        }

        $feedback->save();
        return $feedback;
    }
}