<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        return $this->render('WorkInProgress');
    }
}
