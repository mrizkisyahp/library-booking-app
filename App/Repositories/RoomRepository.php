<?php

namespace App\Repositories;

use App\Models\Room;
use App\Models\Booking;
use App\Core\Paginator;
use App\Core\Database;
use App\Core\QueryBuilder;
use App\Repositories\BookingRepository;
use Carbon\Carbon;

class RoomRepository
{
    public function __construct(
        private Database $database,
        private BookingRepository $bookingRepository
    ) {
    }

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

    public function getAll(array $filters = [], int $perPage = 15, int $page = 1, bool $isAdmin = false, bool $isDosen = false): Paginator
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

        // Admin sees all rooms
        if ($isAdmin) {
            return $query->orderBy('nama_ruangan', 'asc')->paginate($perPage, $page);
        }

        // Dosen/Tendik can see available AND adminOnly rooms
        if ($isDosen) {
            return $query->whereIn('status_ruangan', ['available', 'adminOnly'])->orderBy('nama_ruangan', 'asc')->paginate($perPage, $page);
        }

        // Students can only see available rooms (not adminOnly)
        return $query->where('status_ruangan', 'available')->orderBy('nama_ruangan', 'asc')->paginate($perPage, $page);
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
        $startDate = Carbon::today();
        $endDate = $startDate->copy();

        // Calculate end date (skip weekends)
        $addedDays = 0;
        while ($addedDays < $days) {
            $endDate->addDay();
            if ($endDate->isWeekday()) {
                $addedDays++;
            }
        }

        // Get bookings for the period
        $bookings = Booking::query()
            ->where('ruangan_id', $roomId)
            ->where('tanggal_penggunaan_ruang', '>=', $startDate->format('Y-m-d'))
            ->where('tanggal_penggunaan_ruang', '<=', $endDate->format('Y-m-d'))
            ->whereIn('status', ['verified', 'active'])
            ->orderBy('tanggal_penggunaan_ruang', 'ASC')
            ->orderBy('waktu_mulai', 'ASC')
            ->get();

        // Get blocked dates for the period
        $blockedDatesData = $this->bookingRepository->getBlockedDates();
        $blockedDates = [];
        $blockingReasons = [];

        foreach ($blockedDatesData as $blocked) {
            if ($blocked['ruangan_id'] == $roomId || $blocked['ruangan_id'] === null) {
                $begin = Carbon::parse($blocked['tanggal_begin']);
                $end = Carbon::parse($blocked['tanggal_end']);

                // Add all dates in the blocked range
                for ($date = $begin->copy(); $date->lte($end); $date->addDay()) {
                    $dateKey = $date->format('Y-m-d');
                    $blockedDates[] = $dateKey;
                    $blockingReasons[$dateKey] = $blocked['alasan'] ?? 'Blocked by admin';
                }
            }
        }

        $calendar = [];
        $currentDate = Carbon::today();

        while (count($calendar) < $days) {
            // Skip weekends
            if ($currentDate->isWeekday()) {
                $dateStr = $currentDate->format('Y-m-d');

                // Determine availability status
                $isBlocked = in_array($dateStr, $blockedDates);
                $dayBookings = [];

                foreach ($bookings as $booking) {
                    if ($booking->tanggal_penggunaan_ruang === $dateStr) {
                        $dayBookings[] = [
                            'waktu_mulai' => $booking->waktu_mulai,
                            'waktu_selesai' => $booking->waktu_selesai,
                            'status_booking' => $booking->status,
                        ];
                    }
                }

                // Determine availability status (only 2 states: available or blocked)
                $isBlocked = in_array($dateStr, $blockedDates);
                $availabilityStatus = $isBlocked ? 'blocked' : 'available';

                $calendar[] = [
                    'date' => $dateStr,
                    'day' => $currentDate->format('l'),
                    'day_short' => $currentDate->format('D'),
                    'day_number' => $currentDate->format('d'),
                    'month' => $currentDate->format('M'),
                    'bookings' => $dayBookings,
                    'availability_status' => $availabilityStatus,
                    'is_blocked' => $isBlocked,
                    'blocking_reason' => $isBlocked ? ($blockingReasons[$dateStr] ?? 'Blocked') : null,
                ];
            }

            $currentDate->addDay();
        }

        return $calendar;
    }

    public function getRoomAverageRating(int $roomId): ?float
    {
        $result = (new QueryBuilder($this->database->pdo))
            ->table('booking')
            ->select(['AVG(feedback.rating) as avg_rating'])
            ->leftJoin('feedback', 'booking.id_booking', '=', 'feedback.booking_id')
            ->where('booking.ruangan_id', $roomId)
            ->whereNotNull('feedback.rating')
            ->first();

        return $result && $result['avg_rating'] ? (float) $result['avg_rating'] : null;
    }
}