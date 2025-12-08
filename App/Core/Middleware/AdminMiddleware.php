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
        if (auth()->guest()) {
            flash('error', 'Please login to access this page.');
            redirect('/login');
            return false;
        }

        if (!auth()->user() instanceof User || !auth()->user()->isAdmin()) {
            flash('error', 'Access denied. Admin only.');
            redirect('/dashboard');
            return false;
        }

        return true;
    }
}
