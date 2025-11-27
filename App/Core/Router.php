<?php
namespace App\Core;

use App\Core\Middleware;
use App\Core\Exceptions\NotFoundException;

class Router
{

    public Request $request;
    public Response $response;
    protected array $routes = [];
    private string $controllerNamespace = 'App\\Controllers\\';
    private array $groupStack = [];
    private array $namedRoutes = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, callable|array|string $callback, array $options = []): void
    {
        $this->addRoute('get', $path, $callback, $options);
    }

    public function post(string $path, callable|array|string $callback, array $options = []): void
    {
        $this->addRoute('post', $path, $callback, $options);
    }

    public function group(array $attributes, callable $callback): void
    {
        $this->groupStack[] = $attributes;
        $callback($this);
        array_pop($this->groupStack);
    }

    public function route(string $name, array $params = []): ?string
    {
        $template = $this->namedRoutes[$name] ?? null;
        if (!$template) {
            return null;
        }

        $url = $template;
        foreach ($params as $key => $value) {
            $url = preg_replace('/\{' . preg_quote($key, '/') . '\??(:[^}]+)?\}/', $value, $url);
        }

        return preg_replace('#\{[^}]+\?\}#', '', $url);
    }

    private function matchRoute(string $requestPath, array $routesForMethod): array
    {
        foreach ($routesForMethod as $path => $route) {
            $pattern = preg_replace('#\{(\w+)(:\s*[^}]+)?\}#', '(?P<$1>$2[^/]+)', $path);
            $pattern = preg_replace('#\{(\w+)\?\}#', '(?P<$1>[^/]*)?', $pattern);
            $regex = '#^' . str_replace('/', '\/', $pattern) . '$#';

            if (preg_match($regex, $requestPath, $matches)) {
                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_int($key) && $value !== '') {
                        $params[$key] = $value;
                    }
                }

                return [$route, $params];
            }
        }

        return [null, []];
    }

    private function addRoute(string $method, string $path, callable|array|string $callback, array $options = []): void
    {
        [$fullPath, $routeName, $routeMiddleware] = $this->applyGroupAttributes($path, $options);

        if ($routeName) {
            $this->namedRoutes[$routeName] = $fullPath;
        }

        $this->routes[$method][$fullPath] = [
            'callback' => $callback,
            'middleware' => $routeMiddleware,
            'name' => $routeName,
        ];
    }

    private function applyGroupAttributes(string $path, array $options): array
    {
        $prefix = '';
        $namePrefix = '';
        $middleware = [];

        foreach ($this->groupStack as $group) {
            $groupPrefix = $group['prefix'] ?? '';
            if ($groupPrefix !== '') {
                $prefix .= '/' . trim($groupPrefix, '/');
            }

            $namePrefix .= $group['as'] ?? ($group['name'] ?? '');
            $middleware = array_merge($middleware, $group['middleware'] ?? []);
        }

        $fullPath = $prefix ? rtrim($prefix, '/') . '/' . ltrim($path, '/') : $path;
        $routeName = $namePrefix . ($options['name'] ?? $options['as'] ?? '');
        $routeMiddleware = array_merge($middleware, $options['middleware'] ?? []);

        return [$fullPath, $routeName ?: null, $routeMiddleware];
    }

    private function runMiddleware(array $middlewares): bool
    {
        foreach ($middlewares as $middleware) {
            if (is_string($middleware)) {
                if (!class_exists($middleware)) {
                    $this->response->setStatusCode(500);
                    echo "Middleware Class Not Found: {$middleware}";
                    return false;
                }
                $middleware = new $middleware();
            }

            if ($middleware instanceof Middleware) {
                if (!$middleware->handle($this->request, $this->response)) {
                    return false;
                }
            } elseif (is_callable($middleware)) {
                if ($middleware($this->request, $this->response) === false) {
                    return false;
                }
            } else {
                $this->response->setStatusCode(500);
                echo "Invalid Middleware provided.";
                return false;
            }
        }

        return true;
    }

    public function resolve(): string
    {
        $path = $this->request->getPath();
        $method = $this->request->method();
        [$route, $params] = $this->matchRoute($path, $this->routes[$method] ?? []);
        $this->request->routeParams = $params;
        $callback = $route['callback'] ?? null;

        if ($callback === null) {
            $this->response->setStatusCode(404);
            throw new NotFoundException();
        }

        if (!empty($route['middleware'])) {
            if (!$this->runMiddleware($route['middleware'])) {
                return '';
            }
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

            if (!method_exists($controller, $action)) {
                $this->response->setStatusCode(500);
                return "Controller action not found: {$controllerClass}::{$action}";
            }

            $result = call_user_func_array([$controller, $action], array_merge([$this->request, $this->response], $params));
            return is_string($result) ? $result : '';
        }

        if (is_callable($callback)) {
            return call_user_func_array($callback, array_merge([$this->request, $this->response], $params));
        }

        $this->response->setStatusCode(500);
        return 'Invalid route callback';
    }

    public function renderView(string $view, array $data = []): string
    {
        $layoutsContent = $this->layoutContent();
        $viewContent = $this->renderOnlyView($view, $data);
        return str_replace('{{content}}', $viewContent, $layoutsContent);
    }

    public function renderContent(string $viewContent): string
    {
        $layoutsContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutsContent);
    }

    protected function layoutContent(): string
    {
        $controller = App::$app->controller ?? null;
        $layout = 'main';

        if ($controller && property_exists($controller, 'layout') && !empty($controller->layout)) {
            $layout = $controller->layout;
        }

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

    protected function renderOnlyView(string $view, array $data): string
    {
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        ob_start();
        include_once App::$ROOT_DIR . "/App/Views/{$view}.php";
        return ob_get_clean();
    }
}
