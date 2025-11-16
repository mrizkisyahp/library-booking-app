<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Services\FeedbackService;
use App\Models\User;

class UserFeedbackController extends Controller
{
    protected ?User $currentUser = null;
    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware(['create', 'store']));
        $this->currentUser = App::$app->user instanceof User ? App::$app->user : null;
    }

    public function create(Request $request, Response $response): string
    {
        $user = $this->currentUser;
        if (!$user instanceof User) {
            $response->redirect('/login');
            return '';
        }

        $bookingId = (int)($request->getBody()['booking'] ?? 0);
        $service = new FeedbackService();
        $result = $service->getFeedbackForm($bookingId, (int)$user->id_user);

        if (!$result['success']) {
            App::$app->session->setFlash('error', $result['message'] ?? 'Booking tidak valid.');
            $response->redirect($result['redirect'] ?? '/dashboard');
            return '';
        }

        $this->setLayout('main');
        $this->setTitle('Feedback Booking');
        return $this->render('User/Feedback/Create', $result['data'] ?? []);
    }

    public function store(Request $request, Response $response)
    {
        $user = $this->currentUser;
        if (!$request->isPost()) {
            $response->redirect('/dashboard');
            return;
        }

        if (!$this->currentUser instanceof User) {
            $response->redirect('/login');
            return;
        }

        $bookingId = (int)($request->getBody()['booking_id'] ?? 0);
        $service = new FeedbackService();
        $result = $service->submitFeedback($bookingId, (int)$user->id_user, $request->getBody());

        App::$app->session->setFlash($result['success'] ? 'success' : 'error', $result['message'] ?? '');
        $response->redirect($result['redirect'] ?? '/dashboard');
    }
}
