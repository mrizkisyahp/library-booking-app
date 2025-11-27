<?php

namespace App\Core;

class Session
{
    protected const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
                || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

            $lifetime = (int) ($_ENV['SESSION_LIFETIME'] ?? 7200);

            session_set_cookie_params([
                'lifetime' => $lifetime,
                'path' => '/',
                'domain' => '',
                'secure' => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);

            session_start();
        }

        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function setFlash(string $key, $message): void
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function flash(string $key, $value): void
    {
        $this->setFlash($key, $value);
    }

    public function getFlashData(): array
    {
        return $_SESSION[self::FLASH_KEY] ?? [];
    }

    public function keep(array $keys): void
    {
        if (!isset($_SESSION[self::FLASH_KEY]))
            return;
        foreach ($keys as $key) {
            if (isset($_SESSION[self::FLASH_KEY][$key])) {
                $_SESSION[self::FLASH_KEY][$key]['remove'] = false;
            }
        }
    }

    public function getFlash(string $key, $default = null): string|false
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? $default;
    }

    public function __destruct()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

        foreach ($flashMessages as $key => &$flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }

        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function set($key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function remove($key): void
    {
        unset($_SESSION[$key]);
    }
}
