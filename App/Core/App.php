<?php

namespace App\Core;

use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ForbiddenException;
use App\Core\Services\AuthService;
use App\Core\Services\Logger;
use App\Models\User;

class App
{
    public static string $ROOT_DIR;
    public string $userClass;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public Logger $log;
    public static App $app;
    public ?Controller $controller = null;
    public ?DbModel $user;
    public AuthService $auth;
    public static function getBaseUrl(): string {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        return rtrim(str_replace('//', '/', $scriptDir), '/');
    }

    public function __construct($rootPath, array $config)
    {
        $this->userClass = $config['userClass'] ?? User::class;
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;

        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->log = new Logger();
        $this->router = new Router($this->request, $this->response);

        $dbConfig = $config['database'] ?? $config['db'] ?? [];
        
        $this->db = new Database($dbConfig);

        $this->auth = new AuthService($this->session, $this->userClass);
        $this->auth->bootstrap();
        $this->user = $this->auth->getUser();
    }

    public function run(): void
{
    try {
        echo $this->router->resolve();

    } catch (NotFoundException $e) {
        http_response_code(404);
        $isDev = ($_ENV['APP_ENV'] ?? 'production') === 'development';

        echo $this->router->renderView('errors/404', [
            'message' => $isDev ? $e->getMessage() : null,
            'file'    => $isDev ? $e->getFile() : null,
            'line'    => $isDev ? $e->getLine() : null,
            'trace'   => $isDev ? $e->getTraceAsString() : null,
        ]);

    } catch (ForbiddenException $e) {
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
            Logger::error('Unhandled exception', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);
        }

        echo $this->router->renderView('errors/500', $context);
    }
}
}
    
