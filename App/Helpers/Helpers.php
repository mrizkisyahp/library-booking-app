<?php

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

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
    function auth(): mixed
    {
        return App::$app->user;
    }
}

if (!function_exists('user')) {
    function user(): mixed
    {
        return App::$app->user;
    }
}

if (!function_exists('guest')) {
    function guest(): bool
    {
        return App::$app->user === null;
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
            var_dump($var);
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
            var_dump($var);
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
