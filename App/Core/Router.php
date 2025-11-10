<?php
namespace App\Core;

use App\Core\Exceptions\NotFoundException;

class Router {

    public Request $request;
    public Response $response;
    protected array $routes = [];
    private string $controllerNamespace = 'App\\Controllers\\';

    public function __construct(Request $request, Response $response) {
        $this->request  = $request;
        $this->response = $response;
    }

    public function get(string $path, callable|array|string $callback): void {
        $this->routes['get'][$path] = $callback;
    }

    public function post(string $path, callable|array|string $callback): void {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve(): string {
        $path   = $this->request->getPath();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? null;

        if ($callback === null) {
            $this->response->setStatusCode(404);
            throw new NotFoundException();
        }

        if (is_string($callback)) {
            return $this->renderView($callback);
        }

        if (is_array($callback)) {
            [$controllerClass, $action] = $callback;

            if (is_string($action) && str_contains($action, '/')) {
                $this->response->setStatusCode(500);
                return "Invalid action name '{$action}'. Use a single method name like 'index'.";
            }

            if (is_string($controllerClass) && !str_contains($controllerClass, '\\')) {
                $controllerClass = $this->controllerNamespace . $controllerClass;
            }

            if (!class_exists($controllerClass)) {
                $this->response->setStatusCode(500);
                return "Controller class not found: {$controllerClass}";
            }

            $controller = new $controllerClass();
            App::$app->controller = $controller;
            App::$app->controller->action = $action;

            foreach ($controller->getMiddlewares() as $middleware) {
                if (!$middleware->handle($this->request, $this->response)) {
                    return '';
                }
            }

            if (!method_exists($controller, $action)) {
                $this->response->setStatusCode(500);
                return "Controller action not found: {$controllerClass}::{$action}";
            }

            $result = call_user_func([$controller, $action], $this->request, $this->response);
            return is_string($result) ? $result : '';
        }

        if (is_callable($callback)) {
            return call_user_func($callback, $this->request, $this->response);
        }

        $this->response->setStatusCode(500);
        return 'Invalid route callback';
    }

    public function renderView(string $view, array $data = []): string {
        $layoutsContent = $this->layoutContent();
        $viewContent    = $this->renderOnlyView($view, $data);
        return str_replace('{{content}}', $viewContent, $layoutsContent);
    }

    public function renderContent(string $viewContent): string {
        $layoutsContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutsContent);
    }

    protected function layoutContent(): string {
        $controller = App::$app->controller ?? null;
        $layout = 'main';

        if ($controller && property_exists($controller, 'layout') && !empty($controller->layout)) {
            $layout = $controller->layout;
        }

        $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        
        $layoutPath = App::$ROOT_DIR . "/App/Views/Layouts/{$layout}.php";
        if (!file_exists($layoutPath)) {
            $layoutPath = App::$ROOT_DIR . "/App/Views/Layouts/" . ucfirst($layout) . ".php";
        }

        if (!file_exists($layoutPath)) {
            throw new \RuntimeException("Layout file not found for layout '{$layout}'.");
        }

        ob_start();
        include_once $layoutPath;
        return ob_get_clean();
    }

    protected function renderOnlyView(string $view, array $data): string {
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

        ob_start();
        include_once App::$ROOT_DIR . "/App/Views/{$view}.php";
        return ob_get_clean();
    }
}
