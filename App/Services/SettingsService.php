<?php

namespace App\Services;

use App\Repositories\SettingsRepository;

class SettingsService
{
    private SettingsRepository $repo;
    private ?array $cache = null;

    public function __construct(SettingsRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Get a single setting value with in-memory caching
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $this->loadCache();
        return $this->cache[$key]['value'] ?? $default;
    }

    /**
     * Get all settings
     */
    public function getAll(): array
    {
        $this->loadCache();
        return $this->cache;
    }

    /**
     * Get settings by group as simple key-value array
     */
    public function getByGroup(string $group): array
    {
        return $this->repo->getByGroup($group);
    }

    /**
     * Update settings with validation
     */
    public function updateSettings(array $data): array
    {
        $errors = $this->validateSettings($data);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $this->repo->updateMany($data);
        $this->clearCache();

        return ['success' => true];
    }

    /**
     * Validate settings before save
     */
    private function validateSettings(array $data): array
    {
        $errors = [];

        // Time validations
        if (isset($data['library_open_time'], $data['library_close_time'])) {
            if ($data['library_open_time'] >= $data['library_close_time']) {
                $errors['library_close_time'] = 'Jam tutup harus setelah jam buka';
            }
        }

        // Duration validations
        if (isset($data['min_booking_duration'], $data['max_booking_duration'])) {
            $min = (int) $data['min_booking_duration'];
            $max = (int) $data['max_booking_duration'];

            if ($min < 1) {
                $errors['min_booking_duration'] = 'Durasi minimal harus minimal 1 menit';
            }
            if ($max > 480) {
                $errors['max_booking_duration'] = 'Durasi maksimal tidak boleh lebih dari 8 jam';
            }
            if ($min > $max) {
                $errors['min_booking_duration'] = 'Durasi minimal tidak boleh lebih dari maksimal';
            }
        }

        // Break time validations
        if (isset($data['break_start_weekday'], $data['break_end_weekday'])) {
            if ($data['break_start_weekday'] >= $data['break_end_weekday']) {
                $errors['break_end_weekday'] = 'Jam selesai istirahat harus setelah jam mulai';
            }
        }

        if (isset($data['break_start_friday'], $data['break_end_friday'])) {
            if ($data['break_start_friday'] >= $data['break_end_friday']) {
                $errors['break_end_friday'] = 'Jam selesai istirahat Jumat harus setelah jam mulai';
            }
        }

        return $errors;
    }

    /**
     * Get operating days as array of active day numbers (0=Sunday, 1=Monday, etc.)
     */
    public function getActiveDays(): array
    {
        $days = [];
        $dayMap = [
            'operating_day_monday' => 1,
            'operating_day_tuesday' => 2,
            'operating_day_wednesday' => 3,
            'operating_day_thursday' => 4,
            'operating_day_friday' => 5,
            'operating_day_saturday' => 6,
            'operating_day_sunday' => 0,
        ];

        foreach ($dayMap as $key => $dayNum) {
            if ($this->get($key, false)) {
                $days[] = $dayNum;
            }
        }

        return $days;
    }

    /**
     * Get booking time range (accounting for buffers)
     */
    public function getBookingTimeRange(): array
    {
        $openTime = $this->get('library_open_time', '08:00');
        $closeTime = $this->get('library_close_time', '16:20');
        $startBuffer = (int) $this->get('booking_start_buffer', 15);
        $endBuffer = (int) $this->get('booking_end_buffer', 20);

        $bookingStart = date('H:i', strtotime($openTime) + ($startBuffer * 60));
        $bookingEnd = date('H:i', strtotime($closeTime) - ($endBuffer * 60));

        return [
            'open' => $openTime,
            'close' => $closeTime,
            'booking_start' => $bookingStart,
            'booking_end' => $bookingEnd,
        ];
    }

    /**
     * Get break time for a specific day
     */
    public function getBreakTime(int $dayOfWeek): array
    {
        if ($dayOfWeek === 5) { // Friday
            return [
                'start' => $this->get('break_start_friday', '11:00'),
                'end' => $this->get('break_end_friday', '13:00'),
            ];
        }

        return [
            'start' => $this->get('break_start_weekday', '11:00'),
            'end' => $this->get('break_end_weekday', '12:00'),
        ];
    }

    /**
     * Load all settings into cache
     */
    private function loadCache(): void
    {
        if ($this->cache === null) {
            $this->cache = $this->repo->getAll();
        }
    }

    /**
     * Clear the cache (call after updates)
     */
    public function clearCache(): void
    {
        $this->cache = null;
    }
}
