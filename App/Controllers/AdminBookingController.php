<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Services\BookingService;
use Exception;

class AdminBookingController extends Controller
{
    public function __construct(
        private BookingService $bookingService,
    ) {
    }

    public function index(Request $request)
    {
        $filters = $request->query();
        $page = (int) ($filters['page'] ?? 1);
        $keyword = $request->input('keyword') ?? '';
        $status = $request->input('status') ?? '';
        $view = $request->input('view') ?? 'today'; // 'all' or 'today' - default is today

        $filters = [
            'keyword' => $keyword,
            'status' => $status,
        ];

        // Determine which method to call based on view type
        if ($view === 'today') {
            $paginatedBookings = $this->bookingService->getTodayBookings($filters, 15, $page);
        } else {
            $paginatedBookings = $this->bookingService->getAllBookings($filters, 15, $page);
        }

        // Get status counts for filter buttons (based on current view)
        if ($view === 'today') {
            $statusCounts = $this->bookingService->getTodayStatusCounts();
        } else {
            $statusCounts = $this->bookingService->getStatusCounts();
        }

        return view('Admin/Bookings/Index', [
            'bookings' => $paginatedBookings->items,
            'pagination' => $paginatedBookings,
            'filters' => $filters,
            'activeView' => $view,
            'activeStatus' => $status,
            'statusCounts' => $statusCounts,
        ]);
    }

    public function create(Request $request)
    {
        $rooms = $this->bookingService->getAllRooms();
        $users = $this->bookingService->getAllUsers();

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

            $booking = $this->bookingService->adminCreateBooking($data, $targetUserId);

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
            $bookingId = (int) $request->query('id');
            $page = (int) $request->query('page', 1);
            $perPage = 6;

            $data = $this->bookingService->getBookingForUser($bookingId, 0, true, $page, $perPage);

            $rooms = $this->bookingService->getAllRooms();

            return view('Admin/Bookings/Edit', [
                'booking' => $data['booking'],
                'members' => $data['allMembers'], // Paginator (view iterates items directly if updated, or we use standard loop)
                'allMembers' => $data['allMembers'],
                'pic' => $data['pic'],
                'pagination' => $data['allMembers'], // Alias for pagination snippet
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

            $this->bookingService->adminUpdateBooking($bookingId, $data);

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

            $this->bookingService->deleteBooking($bookingId);

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
            $bookingId = (int) $request->query('id'); // Using query('id') based on prev pattern
            $page = (int) $request->query('page', 1);
            $perPage = 6;

            $data = $this->bookingService->getBookingForUser($bookingId, 0, true, $page, $perPage);

            $rescheduleRequest = $this->bookingService->getPendingRescheduleRequest($bookingId);

            return view('Admin/Bookings/Detail', [
                'bookings' => $data['booking'],
                'pic' => $data['pic'],
                'allMembers' => $data['allMembers'], // Paginator object
                'pagination' => $data['allMembers'], // Alias for pagination UI
                'rescheduleRequest' => $rescheduleRequest
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
            $this->bookingService->approveBooking($bookingId);

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

            $this->bookingService->rejectBooking($bookingId, $reason);

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

            $this->bookingService->completeBooking($bookingId);

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

            $this->bookingService->activateBooking($bookingId, $checkinCode);

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

            $this->bookingService->cancelBooking($bookingId, $user->id_user, $reason);

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

            $this->bookingService->handleNoShow($bookingId);

            flash('success', 'Booking ditandai sebagai no-show');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function blockedDates(Request $request)
    {
        $blockedDates = $this->bookingService->getBlockedDates();
        $rooms = $this->bookingService->getAllRooms();

        return view('Admin/Bookings/BlockedDates', [
            'blockedDates' => $blockedDates,
            'rooms' => $rooms,
        ]);
    }

    /**
     * Preview affected bookings before blocking dates
     */
    public function previewBlockDate(Request $request)
    {
        try {
            $data = $request->all();

            // Get room IDs from form (array or empty)
            $ruanganIds = !empty($data['ruangan_ids']) ? array_map('intval', $data['ruangan_ids']) : [];

            // Get affected bookings
            $affectedBookings = $this->bookingService->getAffectedBookings(
                $data['tanggal_begin'],
                $data['tanggal_end'],
                $ruanganIds
            );

            // Get room names for display
            $rooms = $this->bookingService->getAllRooms();
            $selectedRooms = [];
            if (empty($ruanganIds)) {
                $selectedRooms = ['Semua Ruangan'];
            } else {
                foreach ($rooms as $room) {
                    if (in_array($room->id_ruangan, $ruanganIds)) {
                        $selectedRooms[] = $room->nama_ruangan;
                    }
                }
            }

            return view('Admin/Bookings/PreviewBlockDates', [
                'affectedBookings' => $affectedBookings,
                'dateBegin' => $data['tanggal_begin'],
                'dateEnd' => $data['tanggal_end'],
                'ruanganIds' => $ruanganIds,
                'selectedRooms' => $selectedRooms,
                'alasan' => $data['alasan'] ?? '',
            ]);

        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    /**
     * Confirm and execute blocking with cancellations
     */
    public function blockDate(Request $request)
    {
        try {
            $user = auth()->user();
            $data = $request->all();

            // Get room IDs from form
            $ruanganIds = !empty($data['ruangan_ids']) ? array_map('intval', $data['ruangan_ids']) : [];

            // Block dates and cancel affected bookings
            $cancelledBookings = $this->bookingService->blockDateRangeWithCancellation(
                $data['tanggal_begin'],
                $data['tanggal_end'],
                $ruanganIds,
                $data['alasan'] ?? 'Diblokir oleh admin',
                $user->id_user
            );

            $count = count($cancelledBookings);
            flash('success', "Tanggal berhasil diblokir. {$count} booking dibatalkan dan notifikasi email dikirim.");
            redirect('/admin/blocked-dates');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function unblockDate(Request $request)
    {
        try {
            $blockedDateId = (int) $request->all()['blocked_date_id'];
            $this->bookingService->unblockDate($blockedDateId);

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

            $this->bookingService->addMemberByIdentifier($bookingId, $identifier);

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

            $this->bookingService->kickMember($bookingId, $memberId, auth()->user()->id_user);

            flash('success', 'Anggota berhasil dikeluarkan');
            redirect('/admin/bookings/edit?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function showRescheduleForm(Request $request)
    {
        try {
            $bookingId = (int) $request->query('id');
            $booking = $this->bookingService->getBookingById($bookingId);

            if (!$booking) {
                flash('error', 'Booking tidak ditemukan');
                redirect('/admin/bookings');
            }

            if ($booking->status !== 'verified') {
                flash('error', 'Hanya booking dengan status verified yang dapat di-reschedule');
                redirect('/admin/bookings/detail?id=' . $bookingId);
            }

            return view('Admin/Bookings/Reschedule', [
                'booking' => $booking,
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/admin/bookings');
        }
    }

    public function reschedule(Request $request)
    {
        try {
            $user = auth()->user();
            $bookingId = (int) $request->all()['booking_id'];

            $newData = [
                'tanggal_penggunaan_ruang' => $request->all()['tanggal_penggunaan_ruang'],
                'waktu_mulai' => $request->all()['waktu_mulai'],
                'waktu_selesai' => $request->all()['waktu_selesai'],
            ];

            $this->bookingService->rescheduleBooking($bookingId, $newData, $user->id_user);

            flash('success', 'Booking berhasil di-reschedule. Status kembali ke pending.');
            redirect('/admin/bookings/detail?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function approveReschedule(Request $request)
    {
        try {
            $user = auth()->user();
            $requestId = (int) $request->all()['request_id'];

            $this->bookingService->approveRescheduleRequest($requestId, $user->id_user);

            flash('success', 'Permintaan reschedule disetujui. Booking kembali ke status pending.');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function rejectReschedule(Request $request)
    {
        try {
            $user = auth()->user();
            $requestId = (int) $request->all()['request_id'];
            $reason = $request->all()['reason'] ?? 'Ditolak oleh admin';

            $this->bookingService->rejectRescheduleRequest($requestId, $user->id_user, $reason);

            flash('success', 'Permintaan reschedule ditolak.');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    /**
     * Close library immediately for TODAY
     */
    public function closeToday(Request $request)
    {
        try {
            $user = auth()->user();
            $data = $request->validate([
                'alasan' => 'required|string|min:5',
            ]);

            $today = date('Y-m-d');
            $ruanganIds = []; // Empty array = all rooms

            // Block today and cancel affected bookings
            $cancelledBookings = $this->bookingService->blockDateRangeWithCancellation(
                $today,
                $today,
                $ruanganIds,
                $data['alasan'],
                $user->id_user
            );

            $count = count($cancelledBookings);
            flash('success', "Perpustakaan berhasil ditutup untuk hari ini. {$count} booking dibatalkan/dihapus.");
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    /**
     * Reopen library immediately by removing TODAY's block
     */
    public function reopenToday(Request $request)
    {
        try {
            // Find and delete today's library-wide closure
            $blockedDates = $this->bookingService->getBlockedDates();
            $today = date('Y-m-d');

            foreach ($blockedDates as $block) {
                // Check if this is a library-wide closure for today
                if (
                    $block['ruangan_id'] === null
                    && $block['tanggal_begin'] <= $today
                    && $block['tanggal_end'] >= $today
                ) {
                    $this->bookingService->unblockDate($block['id_blocked_date']);
                    flash('success', 'Perpustakaan berhasil dibuka kembali.');
                    back();
                    return;
                }
            }

            flash('warning', 'Tidak ada penutupan perpustakaan untuk hari ini.');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    /**
     * Delete all blocked dates at once
     */
    public function deleteAllBlocks(Request $request)
    {
        try {
            $blockedDates = $this->bookingService->getBlockedDates();
            $count = 0;

            foreach ($blockedDates as $block) {
                $this->bookingService->unblockDate($block['id_blocked_date']);
                $count++;
            }

            flash('success', "Berhasil menghapus {$count} tanggal yang diblokir.");
            redirect('/admin/blocked-dates');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }
}
