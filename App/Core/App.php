<?php

namespace App\Core;

use App\Core\Middleware;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ForbiddenException;

class App
{
    public static string $ROOT_DIR;
    public string $userClass;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    
    public static App $app;
    public ?Controller $controller = null;
    public ?DbModel $user;
    
    protected array $globalMiddlewares = [];

    public function __construct($rootPath, array $config)
    {
        $this->userClass = $config['userClass'];
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;

        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);

        $dbConfig = $config['database'] ?? $config['db'] ?? [];
        
        $this->db = new Database($dbConfig);

        $primaryValue = $this->session->get('user');
        
        if (!$primaryValue && isset($_COOKIE['remember_user'])) {
            $primaryValue = $_COOKIE['remember_user'];
            if ($primaryValue) {
                $this->session->set('user', $primaryValue);
            }
        }
        
        if ($primaryValue) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        } else {
            $this->user = null;
        }
    }

    public function login(DbModel $user, bool $remember = false): bool
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);

        if ($remember) {
            $lifetime = (int)($_ENV['SESSION_LIFETIME'] ?? 7200);
            setcookie('remember_user', $primaryValue, time() + $lifetime, '/', '', false, true);
        }

        return true;
    }

    public function logout(): void
    {
        $this->user = null;
        $this->session->remove('user');
        
        if (isset($_COOKIE['remember_user'])) {
            setcookie('remember_user', '', time() - 3600, '/');
        }
    }

    public static function isGuest(): bool
    {
        return !self::$app->user;
    }


    public function getTitle(): string
    {
        if ($this->controller && method_exists($this->controller, 'getTitle')) {
            $title = $this->controller->getTitle();
            if (!empty($title)) {
                return $title;
            }
        }

        return match ($this->response->getStatusCode()) {
            403 => '403 Forbidden | Library Booking App',
            404 => '404 Not Found | Library Booking App',
            default => 'Library Booking App',
        };
    }


    public function run(): void
{
    try {
        echo $this->router->resolve();

    } catch (\App\Core\Exceptions\NotFoundException $e) {
        http_response_code(404);
        $isDev = ($_ENV['APP_ENV'] ?? 'production') === 'development';

        echo $this->router->renderView('errors/404', [
            'message' => $isDev ? $e->getMessage() : null,
            'file'    => $isDev ? $e->getFile() : null,
            'line'    => $isDev ? $e->getLine() : null,
            'trace'   => $isDev ? $e->getTraceAsString() : null,
        ]);

    } catch (\App\Core\Exceptions\ForbiddenException $e) {
        http_response_code(403);
        $isDev = ($_ENV['APP_ENV'] ?? 'production') === 'development';

        echo $this->router->renderView('errors/403', [
            'message' => $isDev ? $e->getMessage() : null,
            'file'    => $isDev ? $e->getFile() : null,
            'line'    => $isDev ? $e->getLine() : null,
            'trace'   => $isDev ? $e->getTraceAsString() : null,
        ]);

    } catch (\Throwable $e) {
        http_response_code(500);
        $isDev = ($_ENV['APP_ENV'] ?? 'production') === 'development';

        $context = [
            'message' => $isDev ? $e->getMessage() : null,
            'file'    => $isDev ? $e->getFile() : null,
            'line'    => $isDev ? $e->getLine() : null,
            'trace'   => $isDev ? $e->getTraceAsString() : null,
        ];

        if (class_exists('\App\Core\Services\Logger')) {
            \App\Core\Services\Logger::error('Unhandled exception', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
        }

        echo $this->router->renderView('errors/500', $context);
    }
}

}
    