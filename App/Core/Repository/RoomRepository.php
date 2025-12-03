<?php

namespace App\Core\Repository;

use App\Models\Room;

class RoomRepository
{
    public function getTotalRooms(): int
    {
        return Room::Query()->count();
    }

    public function getAvailableRooms(): int
    {
        return Room::Query()
            ->where('status_ruangan', 'available')
            ->count();
    }

    public function getUnavailableRooms(): int
    {
        return Room::Query()
            ->where('status_ruangan', 'unavailable')
            ->count();
    }
}