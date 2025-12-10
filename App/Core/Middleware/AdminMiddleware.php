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
            flash('error', 'Mohon login terlebih dahulu sebelum mengakses halaman ini.');
            redirect('/login');
            return false;
        }

        if (!auth()->user() instanceof User || !auth()->user()->isAdmin()) {
            flash('error', 'Akses ditolak, anda bukan admin.');
            redirect('/dashboard');
            return false;
        }

        return true;
    }
}
