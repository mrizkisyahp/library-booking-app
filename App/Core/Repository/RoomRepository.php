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

        if (!empty($filters['nama_ruangan'])) {
            $query->where('nama_ruangan', 'like', '%' . $filters['nama_ruangan'] . '%');
        }

        if (!empty($filters['tanggal'])) {
            $query->where('tanggal_penggunaan_ruang', 'like', '%' . $filters['tanggal'] . '%');
        }

        if (!empty($filters['waktu_mulai'])) {
            $query->where('waktu_mulai', 'like', '%' . $filters['waktu_mulai'] . '%');
        }

        if (!empty($filters['status_ruangan'])) {
            $query->where('status_ruangan', $filters['status_ruangan']);
        }

        if (!empty($filters['jenis_ruangan'])) {
            $query->whereIn('jenis_ruangan', $filters['jenis_ruangan']);
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

    public function getAvailability(int $roomId, int $days = 7): array
    {
        $startDate = new \DateTime();
        $endDate = new \DateTime();
        $addedDays = 0;
        while ($addedDays < $days) {
            $endDate->modify('+1 day');
            if ((int) $endDate->format('N') < 6) { // Mon–Fri
                $addedDays++;
            }
        }

        $bookings = Booking::query()
            ->where('ruangan_id', $roomId)
            ->where('tanggal_penggunaan_ruang', '>=', $startDate->format('Y-m-d'))
            ->where('tanggal_penggunaan_ruang', '<=', $endDate->format('Y-m-d'))
            ->whereIn('status', ['verified', 'active'])
            ->orderBy('tanggal_penggunaan_ruang', 'ASC')
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
            if (isset($calendar[$booking->tanggal_penggunaan_ruang])) {
                $calendar[$booking->tanggal_penggunaan_ruang]['bookings'][] = [
                    'waktu_mulai' => $booking->waktu_mulai,
                    'waktu_selesai' => $booking->waktu_selesai,
                    'status_booking' => $booking->status,
                ];
            }
        }

        return array_values($calendar);
    }
}