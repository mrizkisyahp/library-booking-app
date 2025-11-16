<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Services\UserDashboardService;
use App\Models\User;

class UserDashboardController extends Controller
{
    protected ?User $currentUser = null;

    public function __construct()
    {
        $this->registerMiddleware(new AuthMiddleware());
        $this->currentUser = App::$app->user instanceof User ? App::$app->user : null;
    }

    public function index()
    {
        $dashboardService = new UserDashboardService();
        // $dashboardService->expireStaleDrafts();
        $user = $this->currentUser;

        if ((int)$user->id_role === 1) {
            App::$app->response->redirect('/admin');
            return;
        }

        foreach ($dashboardService->computeWarnings($user) as $warning) {
            App::$app->session->setFlash('warning', $warning);
        }

        $userId = (int)$user->id_user;
        $stats = $dashboardService->getBookingStatistics($userId);
        $picBookings = $dashboardService->getPicBookings($userId);
        $memberBookings = $dashboardService->getAnggotaBookings($userId);
        $pendingFeedbacks = $dashboardService->getPendingFeedbacks($userId);

        $bookings = array_merge($picBookings, $memberBookings);
        usort($bookings, static function ($a, $b) {
            return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
        });

        return $this->render('User/UserDashboard', [
            'user' => $user,
            'stats' => $stats,
            'bookings' => $bookings,
            'pendingFeedbacks' => $pendingFeedbacks,
        ]);
    }
}
