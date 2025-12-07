<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Services\BookingServices;
use Exception;

class AdminBookingController extends Controller
{
    public function __construct(
        private BookingServices $bookingServices,
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->query();
        $page = (int) ($filters['page'] ?? 1);
        $keyword = $request->input('keyword') ?? '';
        $status = $request->input('status') ?? '';

        $filters = [
            'keyword' => $keyword,
            'status' => $status,
        ];

        $paginatedBookings = $this->bookingServices->getAllBookings($filters, 15, $page);

        return view('Admin/Bookings/Index', [
            'bookings' => $paginatedBookings->items,
            'pagination' => $paginatedBookings,
            'filters' => $filters,
        ]);
    }

    public function create(Request $request)
    {
        $rooms = $this->bookingServices->getAllRooms();
        $users = $this->bookingServices->getAllUsers();

        return view('Admin/Bookings/Create', [
            'rooms' => $rooms,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $targetUserId = (int) $data['user_id'];

            $booking = $this->bookingServices->adminCreateBooking($data, $targetUserId);

            flash('success', 'Booking berhasil dibuat');
            redirect('/admin/bookings/edit?id=' . $booking->id_booking);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function edit(Request $request)
    {
        try {
            $bookingId = (int) $request->query()['id'];
            $data = $this->bookingServices->getBookingForUser($bookingId, 0, true);

            $rooms = $this->bookingServices->getAllRooms();

            return view('Admin/Bookings/Edit', [
                'booking' => $data['booking'],
                'members' => $data['allMembers'],
                'rooms' => $rooms,
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/admin/bookings');
        }
    }

    public function update(Request $request)
    {
        try {
            $bookingId = (int) $request->all()['booking_id'];
            $data = $request->all();
            unset($data['booking_id'], $data['_token']);

            $this->bookingServices->adminUpdateBooking($bookingId, $data);

            flash('success', 'Booking berhasil diupdate');
            redirect('/admin/bookings/detail?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function delete(Request $request)
    {
        try {
            $bookingId = (int) $request->all()['booking_id'];

            $this->bookingServices->deleteBooking($bookingId);

            flash('success', 'Booking berhasil dihapus');
            redirect('/admin/bookings');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }
    public function detail(Request $request)
    {
        try {
            $bookingId = (int) $request->query()['id'];
            $data = $this->bookingServices->getBookingForUser($bookingId, 0, true); // admin = true

            return view('Admin/Bookings/Detail', [
                'bookings' => $data['booking'],
                'pic' => $data['pic'],
                'members' => $data['members'],
                'allMembers' => $data['allMembers'],
            ]);

        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/admin/bookings');
        }
    }

    public function verify(Request $request)
    {
        try {
            $bookingId = (int) $request->all()['booking_id'];
            $this->bookingServices->approveBooking($bookingId);

            flash('success', 'Booking berhasil diverifikasi');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function reject(Request $request)
    {
        try {
            $bookingId = (int) $request->all()['booking_id'];
            $reason = $request->all()['reason'] ?? 'Ditolak oleh admin';

            $this->bookingServices->rejectBooking($bookingId, $reason);

            flash('success', 'Booking berhasil ditolak');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function complete(Request $request)
    {
        try {
            $bookingId = (int) $request->all()['booking_id'];

            $this->bookingServices->completeBooking($bookingId);

            flash('success', 'Booking berhasil diselesaikan');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }
    public function activate(Request $request)
    {
        try {
            $bookingId = (int) $request->all()['booking_id'];
            $checkinCode = $request->all()['checkin_code'] ?? '';

            $this->bookingServices->activateBooking($bookingId, $checkinCode);

            flash('success', 'Booking berhasil diaktifkan');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function cancel(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->all()['booking_id'];
            $reason = $request->all()['reason'] ?? 'Dibatalkan oleh admin';

            $this->bookingServices->cancelBooking($bookingId, $user->id_user, $reason);

            flash('success', 'Booking berhasil dibatalkan');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function noshow(Request $request)
    {
        try {
            $bookingId = (int) $request->all()['booking_id'];

            $this->bookingServices->handleNoShow($bookingId);

            flash('success', 'Booking ditandai sebagai no-show');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function blockedDates(Request $request)
    {
        $blockedDates = $this->bookingServices->getBlockedDates();

        return view('Admin/Bookings/BlockedDates', [
            'blockedDates' => $blockedDates,
        ]);
    }

    public function blockDate(Request $request)
    {
        try {
            $user = auth()->user();
            $data = $request->all();
            $this->bookingServices->blockDateRange(
                $data['tanggal_begin'],
                $data['tanggal_end'],
                $data['ruangan_id'] ?? null,
                $data['alasan'] ?? 'Diblokir oleh admin',
                $user->id_user
            );

            flash('success', 'Tanggal berhasil diblokir');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function unblockDate(Request $request)
    {
        try {
            $blockedDateId = (int) $request->all()['blocked_date_id'];
            $this->bookingServices->unblockDate($blockedDateId);

            flash('success', 'Tanggal berhasil di-unblock');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function addMember(Request $request)
    {
        try {
            $bookingId = (int) $request->all()['booking_id'];
            $identifier = $request->all()['member_email'];

            $this->bookingServices->addMemberByIdentifier($bookingId, $identifier);

            flash('success', 'Anggota berhasil ditambahkan');
            redirect('/admin/bookings/edit?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }
    public function kickMember(Request $request)
    {
        try {
            $bookingId = (int) $request->all()['booking_id'];
            $memberId = (int) $request->all()['user_id'];

            $this->bookingServices->kickMember($bookingId, $memberId, auth()->user()->id_user);

            flash('success', 'Anggota berhasil dikeluarkan');
            redirect('/admin/bookings/edit?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }
}
