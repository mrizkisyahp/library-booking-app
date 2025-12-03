<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;

class UserBookingController extends Controller
{
    public function showDraft(Request $request)
    {
        return $this->render('WorkInProgress');
    }

    public function createDraft(Request $request)
    {
        flash('info', 'Work in progress');
        redirect('/bookings/draft');
    }

    public function submitDraft(Request $request)
    {
        flash('info', 'Work in progress');
        redirect('/bookings/draft');
    }

    public function addMember(Request $request)
    {
        flash('info', 'Work in progress');
        redirect('/bookings/draft');
    }

    public function showJoinForm(Request $request)
    {
        return $this->render('WorkInProgress');
    }

    public function joinByLink(Request $request)
    {
        flash('info', 'Work in progress');
        redirect('/bookings/join');
    }

    public function showMyBooking(Request $request)
    {
        return $this->render('WorkInProgress');
    }
}
