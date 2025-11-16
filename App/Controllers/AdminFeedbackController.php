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

    public function index(Request $request): string
    {
        $this->setLayout('main');
        $this->setTitle('Feedback Pengguna | Library Booking App');

        $filters = $request->getBody();
        $result = $this->service->listFeedback($filters);

        return $this->render('Admin/Feedback/Index', [
            'feedback' => $result['feedback'] ?? [],
            'filters' => $result['filters'] ?? [],
        ]);
    }

    public function detail(Request $request, Response $response): ?string
    {
        $this->setLayout('main');
        $this->setTitle('Detail Feedback | Library Booking App');

        $id = (int)($request->getBody()['id'] ?? 0);
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
