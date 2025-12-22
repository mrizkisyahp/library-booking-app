<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Exceptions\ValidationException;
use App\Core\Request;
use App\Services\BookingService;
use Exception;

class AdminBookingController extends Controller
{
    private const PER_PAGE_BOOKINGS = 5;
    private const PER_PAGE_MEMBERS = 6;

    public function __construct(
        private BookingService $bookingService,
    ) {
    }

    public function index(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Kelola Booking | Library Booking App');

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
            $paginatedBookings = $this->bookingService->getTodayBookings($filters, self::PER_PAGE_BOOKINGS, $page);
        } else {
            $paginatedBookings = $this->bookingService->getAllBookings($filters, self::PER_PAGE_BOOKINGS, $page);
        }

        // Add pending reschedule flag to each booking
        foreach ($paginatedBookings->items as $booking) {
            $booking->has_pending_reschedule = $this->bookingService->hasPendingRescheduleRequest($booking->id_booking);
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
        $this->setLayout('main');
        $this->setTitle('Buat Booking | Library Booking App');

        $rooms = $this->bookingService->getAllRooms();
        $currentAdminId = auth()->id();

        return view('Admin/Bookings/Create', [
            'rooms' => $rooms,
            'currentAdminId' => $currentAdminId,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|integer|exists:users,id_user',
                'ruangan_id' => 'required|integer|exists:ruangan,id_ruangan',
                'tanggal_penggunaan_ruang' => 'required|date',
                'waktu_mulai' => 'required|date_format:H:i|before:waktu_selesai',
                'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
                'tujuan' => 'required|string|min:5|max:255',
            ]);

            $targetUserId = (int) $data['user_id'];
            unset($data['user_id'], $data['_token']);

            $booking = $this->bookingService->adminCreateBooking($data, $targetUserId);

            flash('success', 'Booking berhasil dibuat');
            redirect('/admin/bookings/edit?id=' . $booking->id_booking);
        } catch (ValidationException $e) {
            $rooms = $this->bookingService->getAllRooms();
            $users = $this->bookingService->getAllUsers();
            $currentAdminId = auth()->id();

            return view('Admin/Bookings/Create', [
                'validator' => $e->getValidator(),
                'rooms' => $rooms,
                'users' => $users,
                'currentAdminId' => $currentAdminId,
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function edit(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Edit Booking | Library Booking App');

        try {
            $bookingId = (int) $request->query('id');
            $page = (int) $request->query('page', 1);
            $perPage = self::PER_PAGE_MEMBERS;

            $data = $this->bookingService->getBookingForUser($bookingId, 0, true, $page, $perPage);

            $rooms = $this->bookingService->getAllRooms();

            return view('Admin/Bookings/Edit', [
                'booking' => $data['booking'],
                'members' => $data['allMembers']->items,
                'pagination' => $data['allMembers'],
                'pic' => $data['pic'],
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
            $data = $request->validate([
                'booking_id' => 'required|integer|exists:booking,id_booking',
                'ruangan_id' => 'required|integer|exists:ruangan,id_ruangan',
                'tanggal_penggunaan_ruang' => 'required|date',
                'waktu_mulai' => 'required|date_format:H:i|before:waktu_selesai',
                'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
                'tujuan' => 'required|string|min:5|max:255',
            ]);

            $bookingId = (int) $data['booking_id'];
            unset($data['booking_id'], $data['_token']);

            $this->bookingService->adminUpdateBooking($bookingId, $data);

            flash('success', 'Booking berhasil diupdate');
            redirect('/admin/bookings/detail?id=' . $bookingId);
        } catch (ValidationException $e) {
            $bookingId = (int) $request->all()['booking_id'];
            $page = (int) $request->query('page', 1);
            $perPage = self::PER_PAGE_MEMBERS;

            $data = $this->bookingService->getBookingForUser($bookingId, 0, true, $page, $perPage);

            $rooms = $this->bookingService->getAllRooms();

            return view('Admin/Bookings/Edit', [
                'booking' => $data['booking'],
                'members' => $data['allMembers']->items,
                'pagination' => $data['allMembers'],
                'pic' => $data['pic'],
                'rooms' => $rooms,
                'validator' => $e->getValidator(),
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function delete(Request $request)
    {
        try {
            $data = $request->validate([
                'booking_id' => 'required|integer|exists:booking,id_booking',
            ]);

            $bookingId = (int) $data['booking_id'];

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
        $this->setLayout('main');
        $this->setTitle('Detail Booking | Library Booking App');

        try {
            $bookingId = (int) $request->query('id');
            $page = (int) $request->query('page', 1);
            $perPage = self::PER_PAGE_MEMBERS;

            $data = $this->bookingService->getBookingForUser($bookingId, 0, true, $page, $perPage);

            $rescheduleRequest = $this->bookingService->getPendingRescheduleRequest($bookingId);

            // Get warning types for assign warning dropdown
            $warningTypes = $this->bookingService->getWarningTypes();

            return view('Admin/Bookings/Detail', [
                'bookings' => $data['booking'],
                'pic' => $data['pic'],
                'allMembers' => $data['allMembers']->items,
                'pagination' => $data['allMembers'],
                'rescheduleRequest' => $rescheduleRequest,
                'warningTypes' => $warningTypes,
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            redirect('/admin/bookings');
        }
    }

    public function verify(Request $request)
    {
        try {
            $data = $request->validate([
                'booking_id' => 'required|integer|exists:booking,id_booking',
            ]);
            $bookingId = (int) $data['booking_id'];

            $this->bookingService->approveBooking($bookingId);

            flash('success', 'Booking berhasil diverifikasi');
            back();
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function complete(Request $request)
    {
        try {
            $data = $request->validate([
                'booking_id' => 'required|integer|exists:booking,id_booking',
            ]);
            $bookingId = (int) $data['booking_id'];

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
            $data = $request->validate([
                'booking_id' => 'required|integer|exists:booking,id_booking',
                'checkin_code' => 'required|string|min:4',
            ]);

            $bookingId = (int) $data['booking_id'];
            $checkinCode = $data['checkin_code'];

            $this->bookingService->activateBooking($bookingId, $checkinCode);

            flash('success', 'Booking berhasil diaktifkan');
            redirect('/admin/bookings/detail?id=' . $bookingId);
        } catch (ValidationException $e) {
            $bookingId = (int) $request->all()['booking_id'];
            $page = (int) $request->query('page', 1);
            $perPage = self::PER_PAGE_MEMBERS;

            $bookingData = $this->bookingService->getBookingForUser($bookingId, 0, true, $page, $perPage);
            $rescheduleRequest = $this->bookingService->getPendingRescheduleRequest($bookingId);

            return view('Admin/Bookings/Detail', [
                'bookings' => $bookingData['booking'],
                'pic' => $bookingData['pic'],
                'allMembers' => $bookingData['allMembers']->items,
                'pagination' => $bookingData['allMembers'],
                'rescheduleRequest' => $rescheduleRequest,
                'validator' => $e->getValidator(),
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function reject(Request $request)
    {
        try {
            $data = $request->validate([
                'booking_id' => 'required|integer|exists:booking,id_booking',
                'reason' => 'required|string|min:5|max:500',
            ]);

            $bookingId = (int) $data['booking_id'];
            $reason = $data['reason'];

            $this->bookingService->rejectBooking($bookingId, $reason);

            flash('success', 'Booking berhasil ditolak');
            redirect('/admin/bookings/detail?id=' . $bookingId);
        } catch (ValidationException $e) {
            $bookingId = (int) $request->all()['booking_id'];
            $page = (int) $request->query('page', 1);
            $perPage = self::PER_PAGE_MEMBERS;

            $bookingData = $this->bookingService->getBookingForUser($bookingId, 0, true, $page, $perPage);
            $rescheduleRequest = $this->bookingService->getPendingRescheduleRequest($bookingId);

            return view('Admin/Bookings/Detail', [
                'bookings' => $bookingData['booking'],
                'pic' => $bookingData['pic'],
                'allMembers' => $bookingData['allMembers']->items,
                'pagination' => $bookingData['allMembers'],
                'rescheduleRequest' => $rescheduleRequest,
                'validator' => $e->getValidator(),
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function rejectPending(Request $request)
    {
        try {
            $data = $request->validate([
                'booking_id' => 'required|integer|exists:booking,id_booking',
                'reason' => 'required|string|min:5|max:500',
            ]);

            $bookingId = (int) $data['booking_id'];
            $reason = $data['reason'];

            $this->bookingService->rejectPendingBooking($bookingId, $reason);

            flash('success', 'Booking dikembalikan ke draft. Email notifikasi telah dikirim ke PIC.');
            redirect('/admin/bookings/detail?id=' . $bookingId);
        } catch (ValidationException $e) {
            $bookingId = (int) $request->all()['booking_id'];
            $page = (int) $request->query('page', 1);
            $perPage = self::PER_PAGE_MEMBERS;

            $bookingData = $this->bookingService->getBookingForUser($bookingId, 0, true, $page, $perPage);
            $rescheduleRequest = $this->bookingService->getPendingRescheduleRequest($bookingId);

            // Get warning types for assign warning dropdown
            $warningTypes = $this->bookingService->getWarningTypes();

            return view('Admin/Bookings/Detail', [
                'bookings' => $bookingData['booking'],
                'pic' => $bookingData['pic'],
                'allMembers' => $bookingData['allMembers']->items,
                'pagination' => $bookingData['allMembers'],
                'rescheduleRequest' => $rescheduleRequest,
                'warningTypes' => $warningTypes,
                'validator' => $e->getValidator(),
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }


    public function noshow(Request $request)
    {
        try {
            $data = $request->validate([
                'booking_id' => 'required|integer|exists:booking,id_booking',
            ]);

            $bookingId = (int) $data['booking_id'];

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
        $this->setLayout('main');
        $this->setTitle('Tanggal Diblokir | Library Booking App');

        $blockedDates = $this->bookingService->getBlockedDates();
        $rooms = $this->bookingService->getAllRooms();

        return view('Admin/Bookings/BlockedDates', [
            'blockedDates' => $blockedDates,
            'rooms' => $rooms,
        ]);
    }

    public function previewBlockDate(Request $request)
    {
        try {
            $data = $request->validate([
                'tanggal_begin' => 'required|date',
                'tanggal_end' => 'required|date|after_or_equal:tanggal_begin',
                'alasan' => 'nullable|string|max:255',
            ]);

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

        } catch (ValidationException $e) {
            $blockedDates = $this->bookingService->getBlockedDates();
            $rooms = $this->bookingService->getAllRooms();

            return view('Admin/Bookings/BlockedDates', [
                'blockedDates' => $blockedDates,
                'rooms' => $rooms,
                'validator' => $e->getValidator(),
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
            $data = $request->validate([
                'tanggal_begin' => 'required|date',
                'tanggal_end' => 'required|date|after_or_equal:tanggal_begin',
                'alasan' => 'nullable|string|max:255',
            ]);

            $user = auth()->user();

            // Get room IDs from form (not validated since array rule not supported)
            $ruanganIds = !empty($request->all()['ruangan_ids']) ? array_map('intval', $request->all()['ruangan_ids']) : [];

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
            $data = $request->validate([
                'blocked_date_id' => 'required|integer',
            ]);

            $this->bookingService->unblockDate((int) $data['blocked_date_id']);

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
            $data = $request->validate([
                'booking_id' => 'required|integer|exists:booking,id_booking',
                'member_email' => 'required|string',
            ]);

            $bookingId = (int) $data['booking_id'];
            $identifier = $data['member_email'];

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
            $data = $request->validate([
                'booking_id' => 'required|integer|exists:booking,id_booking',
                'user_id' => 'required|integer|exists:users,id_user',
            ]);

            $bookingId = (int) $data['booking_id'];
            $memberId = (int) $data['user_id'];

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
            $data = $request->validate([
                'booking_id' => 'required|integer|exists:booking,id_booking',
                'tanggal_penggunaan_ruang' => 'required|date',
                'waktu_mulai' => 'required|date_format:H:i',
                'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            ]);

            $user = auth()->user();
            $bookingId = (int) $data['booking_id'];

            $newData = [
                'tanggal_penggunaan_ruang' => $data['tanggal_penggunaan_ruang'],
                'waktu_mulai' => $data['waktu_mulai'],
                'waktu_selesai' => $data['waktu_selesai'],
            ];

            $this->bookingService->rescheduleBooking($bookingId, $newData, $user->id_user);

            flash('success', 'Booking berhasil di-reschedule. Status kembali ke pending.');
            redirect('/admin/bookings/detail?id=' . $bookingId);
        } catch (ValidationException $e) {
            $bookingId = (int) $request->all()['booking_id'];
            $booking = $this->bookingService->getBookingById($bookingId);

            return view('Admin/Bookings/Reschedule', [
                'booking' => $booking,
                'validator' => $e->getValidator(),
            ]);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    public function approveReschedule(Request $request)
    {
        try {
            $data = $request->validate([
                'request_id' => 'required|integer',
            ]);

            $user = auth()->user();
            $requestId = (int) $data['request_id'];

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
            $data = $request->validate([
                'request_id' => 'required|integer',
                'reason' => 'nullable|string|max:500',
            ]);

            $user = auth()->user();
            $requestId = (int) $data['request_id'];
            $reason = $data['reason'] ?? 'Ditolak oleh admin';

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
     * Handles multiple blocks (library-wide and all-rooms-blocked scenarios)
     */
    public function reopenToday(Request $request)
    {
        try {
            $unblockedCount = $this->bookingService->reopenLibraryToday();

            if ($unblockedCount > 0) {
                flash('success', "Perpustakaan berhasil dibuka kembali. {$unblockedCount} blokir dihapus.");
            } else {
                flash('warning', 'Tidak ada penutupan perpustakaan untuk hari ini.');
            }
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
            $count = $this->bookingService->deleteAllBlockedDates();

            flash('success', "Berhasil menghapus {$count} tanggal yang diblokir.");
            redirect('/admin/blocked-dates');
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }

    /**
     * Assign warnings to all booking members
     */
    public function assignWarning(Request $request)
    {
        try {
            $data = $request->validate([
                'booking_id' => 'required|integer|exists:booking,id_booking',
                'warning_type_id' => 'required|integer',
            ]);

            $bookingId = (int) $data['booking_id'];
            $warningTypeId = (int) $data['warning_type_id'];
            $reason = $request->input('reason') ?? null;

            $warnedUsers = $this->bookingService->assignWarningsToBooking($bookingId, $warningTypeId, $reason);

            $count = count($warnedUsers);
            flash('success', "Peringatan berhasil diberikan ke {$count} anggota: " . implode(', ', $warnedUsers));
            redirect('/admin/bookings/detail?id=' . $bookingId);
        } catch (Exception $e) {
            flash('error', $e->getMessage());
            back();
        }
    }
}
