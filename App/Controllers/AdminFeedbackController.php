<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Repositories\FeedbackRepository;
use Exception;

class AdminFeedbackController extends Controller
{
    private FeedbackRepository $feedbackRepo;

    public function __construct(FeedbackRepository $feedbackRepo)
    {
        $this->feedbackRepo = $feedbackRepo;
    }

    public function index(Request $request)
    {
        try {
            $filters = [
                'keyword' => $request->query()['keyword'] ?? '',
                'rating' => $request->query()['rating'] ?? '',
                'tanggal' => $request->query()['tanggal'] ?? '',
            ];
            $page = (int) ($request->query()['page'] ?? 1);

            $paginator = $this->feedbackRepo->getAllFeedbacks($filters, 15, $page);

            return view('Admin/Feedback/Index', [
                'feedbacks' => $paginator->items,
                'paginator' => $paginator,
                'filters' => $filters,
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/admin');
        }
    }

    public function detail(Request $request)
    {
        try {
            $id = (int) $request->query()['id'];
            $feedback = $this->feedbackRepo->findByIdWithDetails($id);

            if (!$feedback) {
                throw new Exception('Feedback tidak ditemukan');
            }

            return view('Admin/Feedback/Detail', [
                'feedback' => $feedback,
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/admin/feedback');
        }
    }
}
