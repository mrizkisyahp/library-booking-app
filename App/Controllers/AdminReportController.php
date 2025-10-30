<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Middleware\AdminMiddleware;

class AdminReportController extends Controller
{
    public function __construct()
    {
        $this->registerMiddleware(new AdminMiddleware());
    }

    public function index()
    {
        $this->setTitle('Manage Report | Admin');
        $this->setLayout('main');
        echo "Fitur ini akan diimplementasi nanti terima kasih :)";
        exit;
    }

    public function generate()
    {
        echo "Fitur ini akan diimplementasi nanti terima kasih :)";
        exit;
    }
}
