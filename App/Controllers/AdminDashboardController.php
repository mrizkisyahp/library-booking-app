<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Services\DashboardService;
use App\Services\SettingsService;
use App\Services\BookingService;
use App\Services\RoomService;

class AdminDashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
        private SettingsService $settingsService,
        private BookingService $bookingService,
        private RoomService $roomService
    ) {
    }

    public function index(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Dashboard Admin | Library Booking App');

        $data = $this->dashboardService->getAdminDashboardData();
        return $this->render('Admin/AdminDashboard', $data);
    }

    public function settings(Request $request)
    {
        $this->setLayout('main');
        $this->setTitle('Pengaturan | Library Booking App');

        // Get dynamic settings
        $settings = $this->settingsService->getAll();

        // Get blocked dates and rooms for merged view
        $blockedDates = $this->bookingService->getBlockedDates();
        $rooms = $this->roomService->getActiveRooms();

        $libraryClosedToday = libraryIsClosedToday();
        $closureReason = $libraryClosedToday ? getClosureReason(date('Y-m-d')) : null;

        return $this->render('Admin/Settings/Index', [
            'settings' => $settings,
            'blockedDates' => $blockedDates,
            'rooms' => $rooms,
            'libraryClosedToday' => $libraryClosedToday,
            'closureReason' => $closureReason,
        ]);
    }

    public function updateSettings(Request $request, Response $response)
    {
        $data = $request->getBody();

        // Build settings array from form data
        $settingsToUpdate = [];

        // Operating days
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($days as $day) {
            $settingsToUpdate["operating_day_{$day}"] = isset($data["operating_day_{$day}"]) ? '1' : '0';
        }

        // Time settings
        $timeFields = [
            'library_open_time',
            'library_close_time',
            'break_start_weekday',
            'break_end_weekday',
            'break_start_friday',
            'break_end_friday'
        ];
        foreach ($timeFields as $field) {
            if (isset($data[$field])) {
                $settingsToUpdate[$field] = $data[$field];
            }
        }

        // Duration settings
        if (isset($data['min_booking_duration'])) {
            $settingsToUpdate['min_booking_duration'] = (int) $data['min_booking_duration'];
        }
        if (isset($data['max_booking_duration'])) {
            $settingsToUpdate['max_booking_duration'] = (int) $data['max_booking_duration'];
        }

        $result = $this->settingsService->updateSettings($settingsToUpdate);

        if (!$result['success']) {
            foreach ($result['errors'] as $field => $message) {
                flash('error', $message);
            }
            return back();
        }

        flash('success', 'Pengaturan berhasil disimpan');
        return redirect('/admin/settings');
    }
}
