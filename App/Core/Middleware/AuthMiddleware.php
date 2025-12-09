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
        $action = app()->controller->action ?? '';

        if (in_array($action, $this->except, true)) {
            return true;
        }

        if (auth()->guest()) {
            flash('error', 'Please login to access this page.');
            redirect('/login');
            return false;
        }

        $user = user();
        if ($user && in_array($user->status, ['suspended', 'nonaktif'], true)) {
            auth()->logout();
            flash('error', 'Your account is suspended or inactive.');
            redirect('/login');
            return false;
        }

        return true;
    }
}
