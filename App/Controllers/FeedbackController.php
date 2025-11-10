<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Request;
use App\Core\Response;
use App\Models\Booking;
use App\Core\Services\Logger;
use App\Models\Feedback;
use App\Models\User;

class FeedbackController extends Controller
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
        $booking = Booking::findOne($bookingId);

        if (!$booking || $booking->user_id !== (int)$user->id_user || $booking->status !== 'completed') {
            App::$app->session->setFlash('error', 'Booking tidak valid.');
            $response->redirect('/dashboard');
            return '';
        }

        // Prevent duplicate feedback
        $existing = Feedback::findOne(['booking_id' => $bookingId]);
        if ($existing) {
            App::$app->session->setFlash('error', 'Feedback untuk booking ini sudah dikirim.');
            $response->redirect('/dashboard');
            return '';
        }

        $this->setLayout('main');
        $this->setTitle('Feedback Booking');
        return $this->render('User/Feedback/Create', [
            'booking' => $booking,
        ]);
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
        $serviceRating = (int)($request->getBody()['service_rating'] ?? 0);
        $roomRating = (int)($request->getBody()['room_rating'] ?? 0);
        $comments = $request->getBody()['comments'] ?? null;

        $booking = Booking::findOne($bookingId);
        if (!$booking || $booking->user_id !== (int)$user->id_user || $booking->status !== 'completed') {
            App::$app->session->setFlash('error', 'Booking tidak valid.');
            $response->redirect('/dashboard');
            return;
        }

        if ($serviceRating < 1 || $serviceRating > 5 || $roomRating < 1 || $roomRating > 5) {
            App::$app->session->setFlash('error', 'Rating harus di antara 1 sampai 5.');
            $response->redirect('/feedback/create?booking=' . $bookingId);
            return;
        }

        $feedback = new Feedback();
        $feedback->booking_id = $bookingId;
        $feedback->user_id = (int)$user->id_user;
        $feedback->rating = (int)floor(($serviceRating + $roomRating) / 2);
        $feedback->komentar = $comments;

        if ($feedback->save()) {
            Logger::info('Feedback submitted', [
                'user_id' => $user->id_user,
                'booking_id' => $bookingId,
                'rating' => $feedback->rating
            ]);
            App::$app->session->setFlash('success', 'Terima kasih atas feedback Anda.');
        } else {
            Logger::error('Failed to save feedback', [
                'user_id' => $user->id_user,
                'booking_id' => $bookingId
            ]);
            App::$app->session->setFlash('error', 'Gagal menyimpan feedback.');
        }

        $response->redirect('/dashboard');
    }
}
