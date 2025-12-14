<?php

namespace App\Services;

use App\Repositories\RoomRepository;
use App\Models\Room;
use App\Core\Paginator;

class RoomService
{
    public function __construct(
        private RoomRepository $roomRepository
    ) {
    }

    public function getAllRooms(array $filters = [], int $perPage = 15, int $page = 1): Paginator
    {
        $user = auth()->user();
        $isAdmin = $user->id_role === 1;
        $isDosen = $user->isDosen() || $user->isTendik();

        $paginator = $this->roomRepository->getAll($filters, $perPage, $page, $isAdmin, $isDosen);

        // Attach ratings to each room
        foreach ($paginator->items as $room) {
            $room->avg_rating = $this->roomRepository->getRoomAverageRating($room->id_ruangan);
        }

        return $paginator;
    }

    public function getRoomById(int $id): ?Room
    {
        return $this->roomRepository->findById($id);
    }

    public function createRoom(array $data): bool
    {
        if (empty($data['nama_ruangan'])) {
            throw new \Exception('Nama Ruangan harus diisi');
        }

        return $this->roomRepository->create($data);
    }

    public function updateRoom(int $id, array $data): bool
    {
        $room = $this->roomRepository->findById($id);

        if (!$room) {
            throw new \Exception('Ruangan tidak ditemukan');
        }

        return $this->roomRepository->update($id, $data);
    }

    public function deleteRoom(int $id): bool
    {
        $room = $this->roomRepository->findById($id);

        if (!$room) {
            throw new \Exception('Ruangan tidak ditemukan');
        }

        // Check if any room has any booking before deletion?

        return $this->roomRepository->delete($id);
    }

    public function setRoomAvailable(int $id): bool
    {
        $room = $this->roomRepository->findById($id);

        if (!$room) {
            throw new \Exception('Ruangan tidak ditemukan');
        }

        return $this->roomRepository->update($id, ['status_ruangan' => 'available']);
    }

    public function setRoomUnavailable(int $id): bool
    {
        $room = $this->roomRepository->findById($id);

        if (!$room) {
            throw new \Exception('Ruangan tidak ditemukan');
        }

        return $this->roomRepository->update($id, ['status_ruangan' => 'unavailable']);
    }

    public function setRoomAdminOnly(int $id): bool
    {
        $room = $this->roomRepository->findById($id);

        if (!$room) {
            throw new \Exception('Ruangan tidak ditemukan');
        }

        return $this->roomRepository->update($id, ['status_ruangan' => 'adminOnly']);
    }

    public function activateAllRooms(): int
    {
        return $this->roomRepository->activateAll();
    }

    public function deactivateAllRooms(): int
    {
        return $this->roomRepository->deactivateAll();
    }

    public function getRoomAvailability(int $id, int $days): array
    {
        return $this->roomRepository->getAvailability($id, $days);
    }
}