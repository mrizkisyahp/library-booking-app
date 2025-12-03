<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class AdminBookingController extends Controller
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
        redirect('/admin/bookings');
    }

    public function edit(Request $request)
    {
        return $this->render('WorkInProgress');
    }

    public function update(Request $request)
    {
        flash('info', 'Work in progress');
        redirect('/admin/bookings');
    }

    public function delete(Request $request)
    {
        flash('info', 'Work in progress');
        redirect('/admin/bookings');
    }

    public function detail(Request $request)
    {
        return $this->render('WorkInProgress');
    }

    public function verify(Request $request)
    {
        flash('info', 'Work in progress');
        back();
    }

    public function complete(Request $request)
    {
        flash('info', 'Work in progress');
        back();
    }

    public function activate(Request $request)
    {
        flash('info', 'Work in progress');
        back();
    }

    public function cancel(Request $request)
    {
        flash('info', 'Work in progress');
        back();
    }
}
