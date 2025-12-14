<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Exceptions\ValidationException;
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
        $this->setLayout('main');
        $this->setTitle('Berikan Feedback | Library Booking App');

        try {
            $validated = $request->validate([
                'booking' => 'required|integer',
            ]);

            $user = auth()->user();
            $bookingId = (int) $validated['booking'];

            $bookingData = $this->feedbackService->getBookingForFeedback($bookingId, $user->id_user);

            return view('User/Feedback/Create', $bookingData);
        } catch (ValidationException $e) {
            flash('error', 'Booking ID tidak valid.');
            redirect('/my-bookings');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/my-bookings');
        }
    }

    public function store(Request $request)
    {
        $bookingId = null;
        try {
            $validated = $request->validate([
                'booking_id' => 'required|integer',
                'rating' => 'required|integer|min:1|max:5',
                'komentar' => 'nullable|string|max:1000',
            ]);

            $user = auth()->user();
            $bookingId = (int) $validated['booking_id'];

            $data = [
                'rating' => $validated['rating'],
                'komentar' => $validated['komentar'] ?? '',
            ];

            $this->feedbackService->createFeedback($bookingId, $user->id_user, $data);

            flash('success', 'Terima kasih atas feedback Anda!');
            redirect('/my-bookings');
        } catch (ValidationException $e) {
            // Reload view with validation errors
            $user = auth()->user();
            if ($bookingId) {
                try {
                    $bookingData = $this->feedbackService->getBookingForFeedback($bookingId, $user->id_user);
                    return view('User/Feedback/Create', array_merge($bookingData, [
                        'validator' => $e->getValidator(),
                    ]));
                } catch (Exception $ex) {
                    flash('error', 'Booking tidak ditemukan.');
                    redirect('/my-bookings');
                }
            }
            flash('error', 'Data tidak valid.');
            redirect('/my-bookings');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }
}

