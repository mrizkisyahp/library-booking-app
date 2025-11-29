<?php

namespace App\Core;

class Event
{
    private static array $listeners = [];

    public static function listen(string $event, callable $callback): void
    {
        if (!isset(self::$listeners[$event])) {
            self::$listeners[$event] = [];
        }

        self::$listeners[$event][] = $callback;
    }

    public static function dispatch(string $event, mixed $data = null): void
    {
        if (!isset(self::$listeners[$event])) {
            return;
        }

        foreach (self::$listeners[$event] as $callback) {
            $callback($data);
        }
    }

    public static function forget(string $event): void
    {
        unset(self::$listeners[$event]);
    }

    public static function flush(): void
    {
        self::$listeners = [];
    }

    public static function getListeners(string $event): array
    {
        return self::$listeners[$event] ?? [];
    }
}