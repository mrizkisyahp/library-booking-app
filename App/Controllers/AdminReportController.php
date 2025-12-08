<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Middleware\AdminMiddleware;

class AdminReportController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware());
    }
    public function index(Request $request, Response $response)
    {
        $this->setLayout('main');
        $this->setTitle('Report | Library Booking App');

        return $this->render('Admin/Report');
    }
}
