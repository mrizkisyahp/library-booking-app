<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\FeedbackService;
use Exception;

class UserFeedbackController extends Controller
{
    public function __construct(
        private FeedbackService $feedbackService
    ) {
    }

    public function create(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) ($request->query()['booking'] ?? 0);

            $data = $this->feedbackService->getBookingForFeedback($bookingId, $user->id_user);

            return view('User/Feedback/Create', $data);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/my-bookings');
        }
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->all()['booking_id'];

            $data = [
                'rating' => $request->all()['rating'],
                'komentar' => $request->all()['komentar'] ?? '',
            ];

            $this->feedbackService->createFeedback($bookingId, $user->id_user, $data);

            flash('success', 'Terima kasih atas feedback Anda!');
            redirect('/my-bookings');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }
}
