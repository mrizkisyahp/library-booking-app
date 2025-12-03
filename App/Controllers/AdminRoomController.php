<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class AdminRoomController extends Controller
{
    public function index(Request $request)
    {
        return $this->render('WorkInProgress');
    }

    public function create(Request $request)
    {
        return $this->render('WorkInProgress');
    }

    public function store(Request $request)
    {
        flash('info', 'Work in progress');
        redirect('/admin/rooms');
    }

    public function edit(Request $request)
    {
        return $this->render('WorkInProgress');
    }

    public function show(Request $request)
    {
        return $this->render('WorkInProgress');
    }

    public function update(Request $request)
    {
        flash('info', 'Work in progress');
        redirect('/admin/rooms');
    }

    public function delete(Request $request)
    {
        flash('info', 'Work in progress');
        redirect('/admin/rooms');
    }

    public function activate(Request $request)
    {
        flash('info', 'Work in progress');
        back();
    }

    public function deactivate(Request $request)
    {
        flash('info', 'Work in progress');
        back();
    }
}
