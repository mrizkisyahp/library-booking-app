<?php

namespace App\Core\Services;

use App\Core\App;

class CacheService
{
    private string $cacheDir;
    public function __construct(?string $cacheDir = null)
    {
        $this->cacheDir = $cacheDir ?? App::$ROOT_DIR . '/Storage/Cache';
    }

    private function getCachePath(string $key): string
    {
        $dir = $this->cacheDir;
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        return $dir . '/' . md5($key) . '.cache';
    }

    public function set(string $key, mixed $value, int $ttl = 900): bool
    {
        $file = $this->getCachePath($key);
        $data = [
            'value' => $value,
            'expires' => time() + $ttl,
        ];

        return file_put_contents($file, serialize($data)) !== false;
    }

    public function get(string $key): mixed
    {
        $file = $this->getCachePath($key);
        if (!file_exists($file)) {
            return null;
        }

        $data = @unserialize(file_get_contents($file));
        if (!is_array($data) || ($data['expires'] ?? 0) < time()) {
            unlink($file);
            return null;
        }

        return $data['value'] ?? null;
    }

    public function delete(string $key): bool
    {
        $file = $this->getCachePath($key);
        return file_exists($file) ? unlink($file) : true;
    }

    public function clear(): void
    {
        $dir = $this->cacheDir;
        if (!is_dir($dir)) {
            return;
        }

        foreach (glob($dir . '/*.cache') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}

