<?php

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use Carbon\Carbon;


if (!function_exists('app')) {
    function app(): App
    {
        return App::$app;
    }
}

if (!function_exists('request')) {
    function request(): Request
    {
        return App::$app->request;
    }
}

if (!function_exists('response')) {
    function response(): Response
    {
        return App::$app->response;
    }
}

if (!function_exists('session')) {
    function session(?string $key = null, mixed $default = null): mixed
    {
        $session = App::$app->session;

        if ($key === null) {
            return $session;
        }

        return $session->get($key, $default);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        App::$app->response->redirect($url);
    }
}

if (!function_exists('back')) {
    function back(): void
    {
        App::$app->response->back();
    }
}

if (!function_exists('view')) {
    function view(string $view, array $params = []): string
    {
        return App::$app->controller->render($view, $params);
    }
}

if (!function_exists('auth')) {
    function auth(): \App\Core\Services\AuthService
    {
        return App::$app->auth;
    }
}

if (!function_exists('user')) {
    function user(): ?\App\Models\User
    {
        return App::$app->auth->user();
    }
}

if (!function_exists('guest')) {
    function guest(): bool
    {
        return App::$app->auth->guest();
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = null): mixed
    {
        return App::$app->session->getFlash('old_' . $key, $default);
    }
}

if (!function_exists('flash')) {
    function flash(?string $key, mixed $value = null): mixed
    {
        $session = App::$app->session;

        if ($key === null) {
            return $session;
        }

        if ($value === null) {
            return $session->getFlash($key);
        }

        $session->setFlash($key, $value);
        return null;
    }
}

if (!function_exists('config')) {
    /**
     * Get config value from environment
     */
    function config(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('env')) {
    /**
     * Get environment variable
     */
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die
     */
    function dd(mixed ...$vars): void
    {
        foreach ($vars as $var) {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
        }
        die(1);
    }
}

if (!function_exists('dump')) {
    /**
     * Dump variable
     */
    function dump(mixed ...$vars): void
    {
        foreach ($vars as $var) {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
        }
    }
}

if (!function_exists('abort')) {
    /**
     * Abort with HTTP status code
     */
    function abort(int $code = 404, string $message = ''): void
    {
        App::$app->response->abort($code, $message);
    }
}

if (!function_exists('url')) {
    /**
     * Generate URL
     */
    function url(string $path = ''): string
    {
        $baseUrl = rtrim($_ENV['APP_URL'] ?? '', '/');
        $path = ltrim($path, '/');

        return $baseUrl . '/' . $path;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate asset URL
     */
    function asset(string $path): string
    {
        return url($path);
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get CSRF token
     */
    function csrf_token(): string
    {
        $token = session('_token');

        if (!$token) {
            $token = bin2hex(random_bytes(32));
            session()->set('_token', $token);
        }

        return $token;
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        $token = csrf_token();
        return '<input type="hidden" name="_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('dispatch')) {
    /**
     * Dispatch a job to the queue
     */
    function dispatch(\App\Core\Queue\QueueJob $job): string
    {
        return \App\Core\Queue\Queue::push($job);
    }
}

if (!function_exists('container')) {
    /**
     * Get the container instance or resolve a service
     */
    function container(?string $abstract = null): mixed
    {
        $container = app()->container;

        if ($abstract === null) {
            return $container;
        }

        return $container->make($abstract);
    }
}

if (!function_exists('resolve')) {
    /**
     * Resolve a service from the container
     */
    function resolve(string $abstract): mixed
    {
        return app()->container->make($abstract);
    }
}

if (!function_exists('formatWaktu')) {
    function formatWaktu($waktu)
    {
        return Carbon::parse($waktu)->format('H:i') . ' WIB';
    }
}

if (!function_exists('formatTanggal')) {
    function formatTanggal($tanggal)
    {
        return Carbon::parse($tanggal)->translatedFormat('l, d F Y');
    }
}

if (!function_exists('getRemainingAttempts')) {
    function getRemainingAttempts(string $identifier, string $type = 'login'): int
    {
        $cache = app()->container->make(\App\Core\Services\CacheService::class);

        $cacheKey = match ($type) {
            'login' => 'login_attempts_' . md5($identifier),
            'verify' => 'verify_attempts_' . $identifier,
            'reset' => 'reset_attempts_' . $identifier,
            default => 'login_attempts_' . md5($identifier)
        };

        $attempts = (int) ($cache->get($cacheKey) ?? 0);
        return max(0, 5 - $attempts);
    }
}

if (!function_exists('str_slug')) {
    function str_slug(string $string): string
    {
        $slug = preg_replace('/[^A-Za-z0-9]+/', '_', $string);
        return trim($slug, '_');
    }
}
if (!function_exists('room_photos')) {
    function room_photos(\App\Models\Room $room): array
    {
        $dir = App::$ROOT_DIR . '/Public/uploads/Room_Photos/';
        $slug = str_slug($room->nama_ruangan);
        $pattern = $dir . $slug . '_*.{jpg,jpeg,png,webp,svg}';
        $files = glob($pattern, GLOB_BRACE) ?: [];
        sort($files);
        $photos = [];
        foreach ($files as $file) {
            $mime = match (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
                default => 'application/octet-stream',
            };
            $photos[] = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file));
        }
        return $photos;
    }
}
if (!function_exists('room_thumbnail')) {
    function room_thumbnail(\App\Models\Room $room): ?string
    {
        $photos = room_photos($room);
        return $photos[0] ?? null;
    }
}
if (!function_exists('room_facilities')) {
    function room_facilities(\App\Models\Room $room): array
    {
        if (empty($room->deskripsi_ruangan)) {
            return [];
        }
        $parts = preg_split('/[\r\n;,]+/', $room->deskripsi_ruangan);
        return array_values(array_filter(array_map('trim', $parts)));
    }
}
