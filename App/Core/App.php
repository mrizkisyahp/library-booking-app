<?php

namespace App\Core;

use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ForbiddenException;
use App\Core\Exceptions\ValidationException;
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
    public Container $container;
    public static function getBaseUrl(): string
    {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        return rtrim(str_replace('//', '/', $scriptDir), '/');
    }

    public function __construct($rootPath, array $config)
    {
        $this->userClass = $config['userClass'] ?? User::class;
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;

        // Initialize Container
        $this->container = new Container();

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

        // Register core bindings
        $this->container->singleton(Container::class, fn() => $this->container);
        $this->container->singleton(Request::class, fn() => $this->request);
        $this->container->singleton(Response::class, fn() => $this->response);
        $this->container->singleton(Session::class, fn() => $this->session);
        $this->container->singleton(Database::class, fn() => $this->db);
        $this->container->singleton(Router::class, fn() => $this->router);
        $this->container->singleton(AuthService::class, fn() => $this->auth);
    }

    public function run(): void
    {
        try {
            echo $this->router->resolve();

        } catch (NotFoundException $e) {
            $this->response->setStatusCode(404);
            $isDev = ($_ENV['APP_ENV'] ?? 'production') === 'development';

            echo $this->router->renderView('errors/404', [
                'message' => $isDev ? $e->getMessage() : null,
                'file' => $isDev ? $e->getFile() : null,
                'line' => $isDev ? $e->getLine() : null,
                'trace' => $isDev ? $e->getTraceAsString() : null,
            ]);

        } catch (ForbiddenException $e) {
            $this->response->setStatusCode(403);
            $isDev = ($_ENV['APP_ENV'] ?? 'production') === 'development';

            echo $this->router->renderView('errors/403', [
                'message' => $isDev ? $e->getMessage() : null,
                'file' => $isDev ? $e->getFile() : null,
                'line' => $isDev ? $e->getLine() : null,
                'trace' => $isDev ? $e->getTraceAsString() : null,
            ]);

        } catch (ValidationException $e) {
            $this->response->setStatusCode(422);
            $errors = $e->errors();
            $old = $this->request->getBody();
            $isDev = ($_ENV['APP_ENV'] ?? 'production') === 'development';

            if ($this->request->isAjax()) {
                $this->response->json(['errors' => $errors], 422);
            }

            $this->session->setFlash('errors', $errors);
            $this->session->setFlash('old', $old);
            $this->response->back();

        } catch (\Throwable $e) {
            $this->response->setStatusCode(500);
            $isDev = ($_ENV['APP_ENV'] ?? 'production') === 'development';

            $context = [
                'message' => $isDev ? $e->getMessage() : null,
                'file' => $isDev ? $e->getFile() : null,
                'line' => $isDev ? $e->getLine() : null,
                'trace' => $isDev ? $e->getTraceAsString() : null,
            ];

            if (class_exists('\App\Core\Services\Logger')) {
                Logger::error('Unhandled exception', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }

            echo $this->router->renderView('errors/500', $context);
        }
    }
}

