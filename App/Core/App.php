<?php

namespace App\Core;

use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ForbiddenException;
use App\Core\Exceptions\ValidationException;
use App\Core\Services\AuthService;
use App\Core\Services\Logger;
use App\Core\Repository\UserRepository;
use App\Core\Services\TurnstileService;
use App\Core\Services\EmailService;
use App\Core\Services\CacheService;
use App\Models\User;

class App
{
    public static string $ROOT_DIR;
    public static App $app;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public Logger $log;
    public Container $container;
    public ?DbModel $user = null;
    public ?Controller $controller = null;
    public AuthService $auth;
    public string $userClass;

    public static function getBaseUrl(): string
    {
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        return rtrim(str_replace('//', '/', $scriptDir), '/');
    }

    public function __construct(string $rootPath, array $config)
    {
        self::$app = $this;
        self::$ROOT_DIR = $rootPath;

        $this->userClass = $config['userClass'] ?? User::class;

        $this->container = new Container();

        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);

        $dbConfig = $config['database'] ?? [];
        $this->db = new Database($dbConfig);

        $this->container->instance(Container::class, $this->container);
        $this->container->instance(Request::class, $this->request);
        $this->container->instance(Response::class, $this->response);
        $this->container->instance(Session::class, $this->session);
        $this->container->instance(Database::class, $this->db);
        $this->container->instance(Router::class, $this->router);

        $this->container->singleton(
            UserRepository::class,
            fn($c) => new UserRepository($this->db)
        );

        $this->container->singleton(
            TurnstileService::class,
            fn($c) => new TurnstileService(
                $_ENV['TURNSTILE_SECRET'] ?? '',
                filter_var($_ENV['TURNSTILE_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN)
            )
        );

        $this->container->singleton(
            CacheService::class,
            fn($c) => new CacheService(cacheDir: App::$ROOT_DIR . '/Storage/Cache')
        );

        $this->container->singleton(
            Logger::class,
            fn($c) => new Logger(logDir: App::$ROOT_DIR . '/Storage/Logs')
        );

        $mailConfig = $config['mail'] ?? [];
        $this->container->singleton(EmailService::class, function () use ($mailConfig) {
            return new EmailService(
                host: $mailConfig['host'] ?? 'localhost',
                username: $mailConfig['username'] ?? '',
                password: $mailConfig['password'] ?? '',
                encryption: $mailConfig['encryption'] ?? 'tls',
                port: (int) ($mailConfig['port'] ?? 587),
                fromAddress: $mailConfig['from_email'] ?? 'noreply@localhost',
                fromName: $mailConfig['from_name'] ?? 'Library Booking App'
            );
        });

        $this->auth = $this->container->make(AuthService::class, [
            'userClass' => $this->userClass
        ]);

        $this->log = $this->container->make(Logger::class);

        $this->auth->bootstrap();
        $this->user = $this->auth->user();

        $this->container->instance(AuthService::class, $this->auth);
    }

    public function run(): void
    {
        try {
            echo $this->router->resolve();

        } catch (NotFoundException $e) {
            $this->handleNotFoundException($e);

        } catch (ForbiddenException $e) {
            $this->handleForbiddenException($e);

        } catch (ValidationException $e) {
            $this->handleValidationException($e);

        } catch (\PDOException $e) {
            $this->handleDatabaseException($e);

        } catch (\Throwable $e) {
            $this->handleGeneralException($e);
        }
    }

    private function handleNotFoundException(NotFoundException $e): void
    {
        $this->response->setStatusCode(404);
        $this->log->warning('404 Not Found', [
            'uri' => $this->request->getPath(),
            'method' => $this->request->method()
        ]);
        $this->renderError('errors/404', $e, 'Halaman tidak ditemukan');
    }

    private function handleForbiddenException(ForbiddenException $e): void
    {
        $this->response->setStatusCode(403);
        $this->log->warning('403 Forbidden', [
            'uri' => $this->request->getPath(),
            'method' => $this->request->method(),
            'user_id' => $this->user?->id_user ?? null
        ]);
        $this->renderError('errors/403', $e, 'Akses ditolak');
    }

    private function handleValidationException(ValidationException $e): void
    {
        $this->response->setStatusCode(422);

        if ($this->request->isAjax()) {
            $this->response->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
            return;
        }

        $this->session->setFlash('errors', $e->errors());
        foreach ($this->request->getBody() as $key => $value) {
            if (!is_array($value)) {
                $this->session->setFlash('old_' . $key, $value);
            }
        }
        $this->response->back();
    }

    private function handleDatabaseException(\PDOException $e): void
    {
        $this->response->setStatusCode(500);
        $this->log->error('Database Error', [
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'uri' => $this->request->getPath()
        ]);

        $isDev = ($_ENV['APP_ENV'] ?? 'production') === 'development';
        $message = $isDev ? $e->getMessage() : 'A database error occurred';

        $this->renderError('errors/500', $e, $message);
    }

    private function handleGeneralException(\Throwable $e): void
    {
        $this->response->setStatusCode(500);
        $this->log->error('Unhandled Exception', [
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);

        $this->renderError('errors/500', $e, 'Sebuah error tidak terduga terjadi');
    }

    private function renderError(string $view, \Throwable $e, string $defaultMessage = 'Sebuah error terjadi'): void
    {
        $isDev = ($_ENV['APP_ENV'] ?? 'production') === 'development';

        if ($this->request->isAjax()) {
            $this->response->json([
                'success' => false,
                'message' => $isDev ? $e->getMessage() : $defaultMessage,
                'error' => $isDev ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => explode("\n", $e->getTraceAsString())
                ] : null
            ]);
            return;
        }

        echo $this->router->renderView($view, [
            'message' => $isDev ? $e->getMessage() : $defaultMessage,
            'file' => $isDev ? $e->getFile() : null,
            'line' => $isDev ? $e->getLine() : null,
            'trace' => $isDev ? $e->getTraceAsString() : null,
        ]);
    }
}
