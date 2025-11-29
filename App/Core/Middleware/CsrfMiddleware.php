<?php

namespace App\Core\Middleware;

use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;

class CsrfMiddleware extends Middleware
{
    private array $except = [];

    public function __construct(array $except = [])
    {
        $this->except = $except;
    }

    public function handle(Request $request, Response $response): bool
    {
        $method = $request->method();
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
            return true;
        }

        $path = $request->getPath();
        if (in_array($path, $this->except, true)) {
            return true;
        }

        $token = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');

        if (!$this->tokensMatch($token)) {
            flash('error', 'CSRF token mismatch. Please try again.');
            back();
            return false;
        }

        return true;
    }

    private function tokensMatch(?string $token): bool
    {
        $sessionToken = session('_token');

        if (!$sessionToken || !$token) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }
}