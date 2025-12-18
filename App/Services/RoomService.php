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

    /**
     * Get all active rooms (status available or adminOnly)
     */
    public function getActiveRooms(): array
    {
        return Room::Query()
            ->whereIn('status_ruangan', ['available', 'adminOnly'])
            ->whereNull('deleted_at')
            ->orderBy('nama_ruangan', 'ASC')
            ->get();
    }

    public function createRoom(array $data): ?Room
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

        // If room name is changing, rename existing photos
        if (isset($data['nama_ruangan']) && $data['nama_ruangan'] !== $room->nama_ruangan) {
            $this->renameRoomPhotos($room->nama_ruangan, $data['nama_ruangan']);
        }

        return $this->roomRepository->update($id, $data);
    }

    /**
     * Rename room photos when room name changes
     */
    private function renameRoomPhotos(string $oldName, string $newName): void
    {
        $uploadDir = \App\Core\App::$ROOT_DIR . '/Public/uploads/Room_Photos/';

        if (!is_dir($uploadDir)) {
            return; // No photos directory, nothing to do
        }

        $oldSlug = str_slug($oldName);
        $newSlug = str_slug($newName);

        // Find all photos with the old slug pattern
        $pattern = $uploadDir . $oldSlug . '_*.{jpg,jpeg,png,webp}';
        $oldFiles = glob($pattern, GLOB_BRACE) ?: [];

        foreach ($oldFiles as $oldFile) {
            // Extract the index number and extension from filename
            // Example: Ruang_Meeting_1.jpg -> get "1" and "jpg"
            $filename = basename($oldFile);
            if (preg_match('/' . preg_quote($oldSlug, '/') . '_(\d+)\.(\w+)$/', $filename, $matches)) {
                $index = $matches[1];
                $extension = $matches[2];

                $newFilename = $newSlug . '_' . $index . '.' . $extension;
                $newFile = $uploadDir . $newFilename;

                // Rename the file
                if (file_exists($oldFile)) {
                    rename($oldFile, $newFile);
                }
            }
        }
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

    public function uploadRoomImages(int $roomId, array $files): void
    {
        $room = $this->roomRepository->findById($roomId);
        if (!$room) {
            throw new \Exception('Ruangan tidak ditemukan');
        }

        $uploadDir = \App\Core\App::$ROOT_DIR . '/Public/uploads/Room_Photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $slug = str_slug($room->nama_ruangan);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        for ($i = 1; $i <= 4; $i++) {
            $key = 'image_' . $i;

            if (!empty($files[$key]) && $files[$key]['error'] === UPLOAD_ERR_OK) {
                $file = $files[$key];

                // Validation
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (!in_array($mimeType, $allowedTypes)) {
                    throw new \Exception("File {$key} harus berupa gambar (JPG, PNG, WebP)");
                }

                if ($file['size'] > $maxSize) {
                    throw new \Exception("Ukuran file {$key} maksimal 2MB");
                }

                // Extension
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = $slug . '_' . $i . '.' . $extension;

                // Delete old files with same index pattern logic
                // Glob pattern: slug_i.*
                $pattern = $uploadDir . $slug . '_' . $i . '.*';
                $oldFiles = glob($pattern);
                foreach ($oldFiles as $oldFile) {
                    if (is_file($oldFile)) {
                        unlink($oldFile);
                    }
                }

                // Move new file
                if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                    throw new \Exception("Gagal menyimpan file {$key}");
                }
            }
        }
    }
}