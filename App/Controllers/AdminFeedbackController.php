<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Middleware\AdminMiddleware;
use App\Core\Request;
use App\Core\Response;
use App\Core\Services\AdminFeedbackService;

class AdminFeedbackController extends Controller
{
    private AdminFeedbackService $service;

    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware());
    }

    public function index(Request $request, Response $response): ?string
    {
        $this->setLayout('main');
        $this->setTitle('Feedback Pengguna | Library Booking App');

        $filters = [
            'keyword' => $request->getBody()['keyword'] ?? null,
            'tanggal_penggunaan_ruang' => $request->getBody()['tanggal_penggunaan_ruang'] ?? null,
            'rating' => $request->getBody()['rating'] ?? null,
            'page' => (int) ($request->getBody()['page'] ?? 1),
        ];

        $service = new AdminFeedbackService();
        $result = $service->listFeedback($filters);

        if (!$result['success']) {
            App::$app->session->setFlash('error', $result['message'] ?? 'Feedback tidak ditemukan.');
            $response->redirect('/admin/feedback');
            return null;
        }

        $data = $result['data'];

        return $this->render('Admin/Feedback/Index', [
            'feedback' => $data['feedback'] ?? [],
            'filters' => $data['filters'] ?? [],
            'currentPage' => $data['currentPage'],
            'perPage' => $data['perPage'],
            'total' => $data['total'],
        ]);
    }

    public function detail(Request $request, Response $response): ?string
    {
        $this->setLayout('main');
        $this->setTitle('Detail Feedback | Library Booking App');

        $id = (int) ($request->getBody()['id'] ?? 0);
        if ($id <= 0) {
            App::$app->session->setFlash('error', 'ID feedback tidak valid.');
            $response->redirect('/admin/feedback');
            return null;
        }

        $service = new AdminFeedbackService();
        $result = $service->getFeedbackDetail($id);
        $data = $result['data'];

        if (!$result['success']) {
            App::$app->session->setFlash('error', $result['message'] ?? 'Feedback tidak ditemukan.');
            $response->redirect('/admin/feedback');
            return null;
        }

        return $this->render('Admin/Feedback/Detail', $data);
    }
}
