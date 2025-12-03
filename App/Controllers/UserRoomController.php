<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class UserRoomController extends Controller
{
    public function index(Request $request)
    {
        return $this->render('WorkInProgress');
    }

    public function show(Request $request)
    {
        return $this->render('WorkInProgress');
    }
}
