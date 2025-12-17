<?php

namespace App\Core;

use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ForbiddenException;
use App\Core\Exceptions\ValidationException;
use App\Services\AuthService;
use App\Services\Logger;
use App\Services\TurnstileService;
use App\Services\EmailService;
use App\Services\CacheService;
use App\Services\BookingService;
use App\Services\UserService;
use App\Services\ProfileService;
use App\Services\RoomService;
use App\Services\DashboardService;
use App\Services\AdminReportService;
use App\Services\FeedbackService;
use App\Services\SettingsService;

use App\Repositories\UserRepository;
use App\Repositories\BookingRepository;
use App\Repositories\RoomRepository;
use App\Repositories\FeedbackRepository;
use App\Repositories\InvitationRepository;
use App\Repositories\AdminReportRepository;
use App\Repositories\RescheduleRepository;
use App\Repositories\RoleRepository;
use App\Repositories\SettingsRepository;
use App\Repositories\WarningRepository;
use App\Repositories\SuspensionRepository;
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
        $this->container->singleton(BookingRepository::class, fn($c) => new BookingRepository($this->db));
        $this->container->singleton(
            RoomRepository::class,
            fn($c) => new RoomRepository(
                $c->make(Database::class),
                $c->make(BookingRepository::class),
                $c->make(SettingsService::class)
            )
        );
        $this->container->singleton(FeedbackRepository::class, fn($c) => new FeedbackRepository($this->db));
        $this->container->singleton(InvitationRepository::class, fn($c) => new InvitationRepository($this->db));
        $this->container->singleton(AdminReportRepository::class, fn($c) => new AdminReportRepository($this->db));
        $this->container->singleton(RescheduleRepository::class, fn($c) => new RescheduleRepository($this->db));
        $this->container->singleton(RoleRepository::class, fn($c) => new RoleRepository($this->db));
        $this->container->singleton(SettingsRepository::class, fn($c) => new SettingsRepository($this->db));
        $this->container->singleton(WarningRepository::class, fn($c) => new WarningRepository($this->db));
        $this->container->singleton(SuspensionRepository::class, fn($c) => new SuspensionRepository($this->db));

        // Services
        $this->container->singleton(BookingService::class, function ($c) {
            return new BookingService(
                $c->make(BookingRepository::class),
                $c->make(Logger::class),
                $c->make(FeedbackRepository::class),
                $c->make(InvitationRepository::class),
                $c->make(RescheduleRepository::class),
                $this->db,
                $c->make(EmailService::class),
                $c->make(SettingsService::class)
            );
        });
        $this->container->singleton(UserService::class, function ($c) {
            return new UserService(
                $c->make(UserRepository::class),
                $c->make(WarningRepository::class),
                $c->make(SuspensionRepository::class),
                $c->make(Logger::class),
                $c->make(EmailService::class)
            );
        });
        $this->container->singleton(ProfileService::class, fn($c) => new ProfileService($c->make(UserRepository::class)));
        $this->container->singleton(RoomService::class, fn($c) => new RoomService($c->make(RoomRepository::class)));
        $this->container->singleton(DashboardService::class, fn($c) => new DashboardService(
            $c->make(BookingRepository::class),
            $c->make(UserRepository::class),
            $c->make(RoomRepository::class)
        ));
        $this->container->singleton(AdminReportService::class, fn($c) => new AdminReportService($c->make(AdminReportRepository::class)));
        $this->container->singleton(FeedbackService::class, fn($c) => new FeedbackService($c->make(FeedbackRepository::class), $c->make(BookingRepository::class)));
        $this->container->singleton(SettingsService::class, fn($c) => new SettingsService($c->make(SettingsRepository::class)));


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
