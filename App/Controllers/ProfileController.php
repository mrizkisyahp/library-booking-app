<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        return $this->render('WorkInProgress');
    }

    public function uploadKubaca(Request $request)
    {
        flash('info', 'Work in progress');
        redirect('/profile');
    }
}
