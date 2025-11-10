<?php

namespace App\Core;

abstract class Controller
{
    public string $layout = 'main';
    public string $action = '';
    public ?string $title = null;
    protected array $middlewares = [];

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }

    public function render(string $view, array $params = []): string
    {
        return App::$app->router->renderView($view, $params);
    }

    public function registerMiddleware(Middleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

}
