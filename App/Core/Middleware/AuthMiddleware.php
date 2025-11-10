<?php

namespace App\Core\Middleware;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\App;

class AuthMiddleware extends Middleware
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

        if (App::$app->auth->isGuest()) {
            App::$app->session->setFlash('error', 'Please login to access this page.');
            $response->redirect('/login');
            return false;
        }
        return true;
    }
}
