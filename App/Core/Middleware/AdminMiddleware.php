<?php

namespace App\Core\Middleware;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\App;
use App\Models\User;

class AdminMiddleware extends Middleware
{
    public function handle(Request $request, Response $response): bool
    {
        $user = App::$app->auth->getUser();

        if (App::$app->auth->isGuest()) {
            App::$app->session->setFlash('error', 'Please login to access this page.');
            $response->redirect('/login');
            return false;
        }

        if (!$user instanceof User || !$user->isAdmin()) {
            App::$app->session->setFlash('error', 'Access denied. Admin only.');
            $response->redirect('/dashboard');
            return false;
        }
        
        return true;
    }
}
