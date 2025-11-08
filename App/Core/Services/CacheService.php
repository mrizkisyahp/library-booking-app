<?php

namespace App\Core\Services;

use App\Core\App;

class CacheService
{
    private const CACHE_DIR = '/Storage/Cache';

    private static function getCachePath(string $key): string
    {
        $dir = App::$ROOT_DIR . self::CACHE_DIR;
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        return $dir . '/' . md5($key) . '.cache';
    }

    public static function set(string $key, mixed $value, int $ttl = 900): bool
    {
        $file = self::getCachePath($key);
        $data = [
            'value' => $value,
            'expires' => time() + $ttl,
        ];

        return file_put_contents($file, serialize($data)) !== false;
    }

    public static function get(string $key): mixed
    {
        $file = self::getCachePath($key);
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

    public static function delete(string $key): bool
    {
        $file = self::getCachePath($key);
        return file_exists($file) ? unlink($file) : true;
    }

    public static function clear(): void
    {
        $dir = App::$ROOT_DIR . self::CACHE_DIR;
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

