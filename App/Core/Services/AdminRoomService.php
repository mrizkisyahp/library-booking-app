<?php

namespace App\Core\Services;

use App\Core\App;
use App\Models\Room;

class AdminRoomService
{
    private const PER_PAGE = 20;
    private const STATUS_AVAILABLE = 'available';
    private const STATUS_UNAVAILABLE = 'unavailable';
    private const ACTIVE_BOOKING_STATUSES = ['pending', 'verified', 'active'];

    public function listRooms(array $filters = []): array
    {
        $page = max(1, (int)($filters['page'] ?? 1));
        $perPage = (int)($filters['perPage'] ?? self::PER_PAGE);

        $queryFilters = [
            'nama_ruangan' => $filters['keyword'] ?? null,
            'jenis_ruangan' => $filters['jenis_ruangan'] ?? null,
            'status_ruangan' => $filters['status_ruangan'] ?? null,
        ];

        $rooms = Room::findPaginated($page, $perPage, $queryFilters, [
            'only_available' => false,
        ]);

        return [
            'success' => true,
            'data' => [
                'rooms' => $rooms,
                'filters' => $queryFilters,
                'currentPage' => $page,
                'perPage' => $perPage,
                'total' => Room::count($queryFilters),
                'statusOptions' => $this->getStatusOptions(),
            ],
        ];
    }

    public function getStatusOptions(): array
    {
        return [self::STATUS_AVAILABLE, self::STATUS_UNAVAILABLE];
    }

    public function getRoomById(int $id): ?Room
    {
        if ($id <= 0) {
            return null;
        }

        return Room::findOne(['id_ruangan' => $id]);
    }

    public function createRoom(array $data): array
    {
        $room = new Room();
        $this->mapData($room, $data);

        if (!$this->validateRoom($room)) {
            return [
                'success' => false,
                'message' => 'Failed to create room. Please fix the errors.',
                'data' => ['room' => $room],
            ];
        }

        if (!$room->save()) {
            return [
                'success' => false,
                'message' => 'Failed to save room. Please try again.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Room created successfully.',
            'data' => ['room' => $room],
        ];
    }

    public function updateRoom(int $id, array $data): array
    {
        $room = $this->getRoomById($id);
        if (!$room) {
            return ['success' => false, 'message' => 'Room not found.'];
        }

        $this->mapData($room, $data);
        if (!$this->validateRoom($room, $id)) {
            return [
                'success' => false,
                'message' => 'Failed to update room. Please fix the errors.',
                'data' => ['room' => $room],
            ];
        }

        if (!$room->save()) {
            return [
                'success' => false,
                'message' => 'Failed to update room. Please try again.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Room updated successfully.',
            'data' => ['room' => $room],
        ];
    }

    public function deleteRoom(int $id): array
    {
        $room = $this->getRoomById($id);
        if (!$room) {
            return ['success' => false, 'message' => 'Room not found.'];
        }

        if ($this->hasActiveBookings($id)) {
            return [
                'success' => false,
                'message' => 'Cannot delete room with active bookings.',
            ];
        }

        if (!$room->delete()) {
            return [
                'success' => false,
                'message' => 'Failed to delete room.',
            ];
        }

        return ['success' => true, 'message' => 'Room deleted successfully.'];
    }

    public function activateRoom(int $id): array
    {
        return $this->changeStatus($id, self::STATUS_AVAILABLE, 'Room activated.');
    }

    public function deactivateRoom(int $id): array
    {
        return $this->changeStatus($id, self::STATUS_UNAVAILABLE, 'Room deactivated.');
    }

    private function changeStatus(int $id, string $status, string $successMessage): array
    {
        $room = $this->getRoomById($id);
        if (!$room) {
            return ['success' => false, 'message' => 'Room not found.'];
        }

        if ($room->status_ruangan === $status) {
            return [
                'success' => false,
                'message' => 'Room already has this status.',
            ];
        }

        $room->status_ruangan = $status;
        if (!$room->save()) {
            return ['success' => false, 'message' => 'Failed to update room status.'];
        }

        return ['success' => true, 'message' => $successMessage];
    }

    private function mapData(Room $room, array $data): void
    {
        $room->nama_ruangan = trim((string)($data['nama_ruangan'] ?? $room->nama_ruangan));
        $room->kapasitas_min = $this->toInt($data['kapasitas_min'] ?? $room->kapasitas_min);
        $room->kapasitas_max = $this->toInt($data['kapasitas_max'] ?? $room->kapasitas_max);
        $room->jenis_ruangan = trim((string)($data['jenis_ruangan'] ?? $room->jenis_ruangan));
        $room->deskripsi_ruangan = trim((string)($data['deskripsi_ruangan'] ?? $room->deskripsi_ruangan));

        $status = strtolower(trim((string)($data['status_ruangan'] ?? $room->status_ruangan)));
        if (!in_array($status, $this->getStatusOptions(), true)) {
            $status = self::STATUS_AVAILABLE;
        }
        $room->status_ruangan = $status;
    }

    private function validateRoom(Room $room, ?int $roomId = null): bool
    {
        $isValid = true;

        if (!$room->nama_ruangan) {
            $room->addError('nama_ruangan', 'Name is required.');
            $isValid = false;
        }

        if ($room->kapasitas_min === null || $room->kapasitas_min < 1) {
            $room->addError('kapasitas_min', 'Minimum capacity must be greater than 0.');
            $isValid = false;
        }

        if ($room->kapasitas_max === null || $room->kapasitas_max < 1) {
            $room->addError('kapasitas_max', 'Maximum capacity must be greater than 0.');
            $isValid = false;
        }

        if ($room->kapasitas_min !== null && $room->kapasitas_max !== null && $room->kapasitas_min > $room->kapasitas_max) {
            $room->addError('kapasitas_min', 'Minimum capacity cannot exceed maximum capacity.');
            $isValid = false;
        }

        if (!$room->jenis_ruangan) {
            $room->addError('jenis_ruangan', 'Room type is required.');
            $isValid = false;
        }

        if (!$room->deskripsi_ruangan) {
            $room->addError('deskripsi_ruangan', 'Description is required.');
            $isValid = false;
        }

        if (!in_array($room->status_ruangan, $this->getStatusOptions(), true)) {
            $room->addError('status_ruangan', 'Invalid status.');
            $isValid = false;
        }

        $existing = Room::findOne(['nama_ruangan' => $room->nama_ruangan]);
        if ($existing && (int)$existing->id_ruangan !== (int)$roomId) {
            $room->addError('nama_ruangan', 'Room name already exists.');
            $isValid = false;
        }

        return $isValid;
    }

    private function toInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int)$value;
    }

    private function hasActiveBookings(int $roomId): bool
    {
        $placeholders = implode(',', array_fill(0, count(self::ACTIVE_BOOKING_STATUSES), '?'));
        $sql = "SELECT COUNT(*) FROM booking WHERE ruangan_id = ? AND status IN ({$placeholders})";
        $stmt = App::$app->db->prepare($sql);
        $stmt->bindValue(1, $roomId, \PDO::PARAM_INT);

        $index = 2;
        foreach (self::ACTIVE_BOOKING_STATUSES as $status) {
            $stmt->bindValue($index, $status);
            $index++;
        }

        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }
}
