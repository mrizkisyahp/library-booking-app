<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class UserFeedbackController extends Controller
{
    public function create(Request $request)
    {
        return $this->render('WorkInProgress');
    }

    public function store(Request $request)
    {
        flash('info', 'Work in progress');
        redirect('/feedback/create');
    }
}
