<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class AdminFeedbackController extends Controller
{
    public function index(Request $request)
    {
        return $this->render('WorkInProgress');
    }

    public function detail(Request $request)
    {
        return $this->render('WorkInProgress');
    }
}
