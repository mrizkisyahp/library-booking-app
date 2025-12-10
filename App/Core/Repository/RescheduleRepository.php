<?php

namespace App\Core\Repository;

use App\Core\Database;
use App\Models\RescheduleRequest;

class RescheduleRepository
{
    public function __construct(private Database $database)
    {
    }

    public function create(array $data): RescheduleRequest
    {
        $rescheduleRequest = new RescheduleRequest();

        foreach ($data as $key => $value) {
            if (property_exists($rescheduleRequest, $key)) {
                $rescheduleRequest->{$key} = $value;
            }
        }

        $rescheduleRequest->save();
        return $rescheduleRequest;
    }

    public function findById(int $id): ?RescheduleRequest
    {
        return RescheduleRequest::Query()->where('id_request', $id)->first();
    }

    public function findPendingByBookingId(int $bookingId): ?RescheduleRequest
    {
        return RescheduleRequest::Query()->where('booking_id', $bookingId)->where('status', 'pending')->first();
    }

    public function getAllPending(): array
    {
        return RescheduleRequest::Query()->where('status', 'pending')->orderBy('created_at', 'asc')->get();
    }

    public function approve(int $requestId, int $adminId): void
    {

        $request = $this->findById($requestId);

        if ($request) {
            $request->status = 'approved';
            $request->handled_by = $adminId;
            $request->save();
        }
    }

    public function reject(int $requestId, int $adminId, string $reason): void
    {
        $request = $this->findById($requestId);

        if ($request) {
            $request->status = 'rejected';
            $request->handled_by = $adminId;
            $request->reject_reason = $reason;
            $request->save();
        }
    }

    public function delete(int $requestId): void
    {
        RescheduleRequest::Query()->where('id_request', $requestId)->delete();
    }
}