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
        $this->service = new AdminFeedbackService();
    }

    public function index(): string
    {
        $this->setLayout('main');
        $this->setTitle('Feedback Pengguna | Library Booking App');

        $params = App::$app->request->getBody();
        $filters = [
            'nama_ruangan' => $params['nama_ruangan'] ?? '',
            'nama_user' => $params['nama_user'] ?? '',
            'tanggal_penggunaan_ruang' => $params['tanggal_penggunaan_ruang'] ?? '',
            'rating' => $params['rating'] ?? '',
        ];

        $result = $this->service->listFeedback($filters);
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

        $result = $this->service->getFeedbackDetail($id);
        if (!$result['success']) {
            App::$app->session->setFlash('error', $result['message'] ?? 'Feedback tidak ditemukan.');
            $response->redirect('/admin/feedback');
            return null;
        }

        return $this->render('Admin/Feedback/Detail', $result['data']);
    }
}
