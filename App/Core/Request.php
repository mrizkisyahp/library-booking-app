<?php

namespace App\Core;

class Request
{
    public array $routeParams = [];

    public function route(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->routeParams;
        }

        return $this->routeParams[$key] ?? $default;
    }
    public function getPath(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');

        if ($position === false) {
            return $path;
        }

        return substr($path, 0, $position);
    }

    public function method(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet(): bool
    {
        return $this->method() === 'get';
    }

    public function isPost(): bool
    {
        return $this->method() === 'post';
    }

    public function getBody(): array
    {
        $body = [];

        if ($this->method() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_var($value, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
            }
        }

        if ($this->method() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_var($value, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
            }
        }

        return $body;
    }

    public function all(): array
    {
        return $this->getBody();
    }

    public function input(string $key, $default = null)
    {
        $data = $this->getBody();
        return $data[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        $data = $this->getBody();
        return array_key_exists($key, $data);
    }

    public function only(array $keys): array
    {
        $data = $this->getBody();
        return array_intersect_key($data, array_flip($keys));
    }

    public function except(array $keys): array
    {
        $data = $this->getBody();
        return array_diff_key($data, array_flip($keys));
    }

    public function getQuery(): array
    {
        $query = [];
        foreach ($_GET as $key => $value) {
            $query[$key] = filter_var($value, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        }

        return $query;
    }

    public function query(?string $key = null, $default = null)
    {
        if ($key === null)
            return $this->getQuery();
        return $this->getQuery()[$key] ?? $default;
    }

    public function header(string $key, $default = null)
    {
        $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $_SERVER[$serverKey] ?? $default;
    }

    public function isAjax(): bool
    {
        $requestedWith = $this->header('X-Requested-With');
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return strtolower((string) $requestedWith) === 'xmlhttprequest' || str_contains($accept, 'application/json');
    }

    public function boolean(string $key, bool $default = false): bool
    {
        $value = $this->input($key, $default);
        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? (bool) $default;
    }

    public function file(string $key)
    {
        return $_FILES[$key] ?? null;
    }

    public function hasFile(string $key): bool
    {
        return isset($_FILES[$key]) && ($_FILES[$key]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }

    public function validate(array $rules, array $messages = []): array
    {
        $validator = new \App\Core\Validator\Validator();
        return $validator->validate($this->getBody(), $rules, $messages);
    }

    public function ip(): string
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public function userAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
}
