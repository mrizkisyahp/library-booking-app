<?php

namespace App\Core\Middleware;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\App;

class GuestMiddleware extends Middleware
{
    private array $except = [];

    public function __construct(array $except = [])
    {
        $this->except = $except;
    }

    public function handle(Request $request, Response $response): bool
    {
        $action = App::$app->controller->action ?? '';
        
        if (in_array($action, $this->except, true)) {
            return true;
        }

        if (!App::$app->auth->isGuest()) {
            $response->redirect('/dashboard');
            return false;
        }
        return true;
    }
}
