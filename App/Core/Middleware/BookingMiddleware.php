<?php

namespace App\Core\Middleware;

use App\Core\App;
use App\Core\Middleware;
use App\Core\Request;
use App\Core\Response;
use App\Models\Booking;
use App\Models\User;

class BookingMiddleware extends Middleware
{
    public function __construct(private array $actions)
    {
    }

    public function handle(Request $request, Response $response): bool
    {
        if (empty($this->actions)) {
            return true;
        }

        $controller = App::$app->controller;
        $currentAction = $controller?->getAction();

        if ($currentAction === null || !in_array($currentAction, $this->actions, true)) {
            return true;
        }

        $body = $request->getBody();
        $bookingId = (int)($body['booking_id'] ?? $body['id'] ?? $body['booking'] ?? 0);

        if ($bookingId <= 0) {
            App::$app->session->setFlash('error', 'Booking tidak ditemukan.');
            $response->redirect('/dashboard');
            return false;
        }

        $booking = Booking::findOne($bookingId);
        if (!$booking) {
            App::$app->session->setFlash('error', 'Booking tidak ditemukan.');
            $response->redirect('/dashboard');
            return false;
        }

        $currentUser = App::$app->user;
        if (!$currentUser instanceof User) {
            $response->redirect('/login');
            return false;
        }

        if (!$booking->userCanAccess((int)$currentUser->id_user)) {
            App::$app->session->setFlash('error', 'Anda tidak memiliki akses ke booking ini.');
            $response->redirect('/dashboard');
            return false;
        }

        return true;
    }
}
