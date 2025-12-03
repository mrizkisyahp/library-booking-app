<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class AdminUserController extends Controller
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
        redirect('/admin/users');
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
        redirect('/admin/users');
    }

    public function delete(Request $request)
    {
        flash('info', 'Work in progress');
        redirect('/admin/users');
    }

    public function suspend(Request $request)
    {
        flash('info', 'Work in progress');
        back();
    }

    public function unsuspend(Request $request)
    {
        flash('info', 'Work in progress');
        back();
    }

    public function resetPassword(Request $request)
    {
        flash('info', 'Work in progress');
        back();
    }

    public function approveKubaca(Request $request)
    {
        flash('info', 'Work in progress');
        back();
    }

    public function rejectKubaca(Request $request)
    {
        flash('info', 'Work in progress');
        back();
    }
}
