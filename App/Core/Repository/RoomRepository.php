<?php

namespace App\Core\Repository;

use App\Models\Room;
use App\Models\Booking;
use App\Core\Paginator;

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

    public function findById(int $id): ?Room
    {
        return Room::Query()->where('id_ruangan', $id)->first();
    }

    public function getAll(array $filters = [], int $perPage = 15, int $page = 1, bool $isAdmin = false): Paginator
    {
        $query = Room::Query();

        if (!empty($filters['keyword'])) {
            $query->where('nama_ruangan', 'like', '%' . $filters['keyword'] . '%');
        }

        if (!empty($filters['status_ruangan'])) {
            $query->where('status_ruangan', $filters['status_ruangan']);
        }

        if (!empty($filters['jenis_ruangan'])) {
            $query->where('jenis_ruangan', $filters['jenis_ruangan']);
        }

        if (!empty($filters['kapasitas_min'])) {
            $query->where('kapasitas_min', '>=', $filters['kapasitas_min']);
        }

        if (!empty($filters['kapasitas_max'])) {
            $query->where('kapasitas_max', '<=', $filters['kapasitas_max']);
        }

        if (!$isAdmin) {
            return $query->where('status_ruangan', 'available')->whereNotIn('status_ruangan', ['adminOnly'])->orderBy('nama_ruangan', 'asc')->paginate($perPage, $page);
        }

        return $query->orderBy('nama_ruangan', 'asc')->paginate($perPage, $page);
    }

    public function create(array $data): bool
    {
        $room = new Room();

        foreach ($data as $key => $value) {
            if (property_exists($room, $key)) {
                $room->{$key} = $value;
            }
        }

        return $room->save();
    }

    public function delete(int $id): bool
    {
        $room = $this->findById($id);

        if (!$room) {
            return false;
        }

        return $room->delete();
    }

    public function update(int $id, array $data): bool
    {
        $room = $this->findById($id);

        if (!$room) {
            return false;
        }

        foreach ($data as $key => $value) {
            if (property_exists($room, $key)) {
                $room->{$key} = $value;
            }
        }

        return $room->save();
    }

    public function activateAll(): int
    {
        return Room::Query()
            ->where('status_ruangan', 'unavailable')
            ->update(['status_ruangan' => 'available']);
    }

    public function deactivateAll(): int
    {
        return Room::Query()
            ->where('status_ruangan', 'available')
            ->update(['status_ruangan' => 'unavailable']);
    }

    public function getAvailability(int $roomId, int $days = 5): array
    {
        $startDate = new \DateTime();
        $endDate = new \DateTime('+21 days');

        $bookings = Booking::query()
            ->where('ruangan_id', $roomId)
            ->where('tanggal_booking', '>=', $startDate->format('Y-m-d'))
            ->where('tanggal_booking', '<=', $endDate->format('Y-m-d'))
            ->whereNotIn('status', ['draft', 'cancelled', 'noshow'])
            ->orderBy('tanggal_booking', 'ASC')
            ->orderBy('waktu_mulai', 'ASC')
            ->get();

        $calendar = [];
        $added = 0;
        $offset = 0;

        while ($added < $days) {
            $date = (new \DateTime())->modify("+{$offset} days");
            $offset++;

            $dayOfWeek = (int) $date->format('N');
            if ($dayOfWeek === 6 || $dayOfWeek === 7) {
                continue;
            }

            $dateStr = $date->format('Y-m-d');
            $calendar[$dateStr] = [
                'date' => $dateStr,
                'day' => $date->format('l'),
                'day_short' => $date->format('D'),
                'day_number' => $date->format('d'),
                'month' => $date->format('M'),
                'bookings' => [],
            ];
            $added++;
        }

        foreach ($bookings as $booking) {
            if (isset($calendar[$booking->tanggal_booking])) {
                $calendar[$booking->tanggal_booking]['bookings'][] = [
                    'waktu_mulai' => $booking->waktu_mulai,
                    'waktu_selesai' => $booking->waktu_selesai,
                    'status_booking' => $booking->status_booking,
                ];
            }
        }

        return array_values($calendar);
    }
}