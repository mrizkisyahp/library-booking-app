<?php

namespace App\Routes;

use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\VerifyController;
use App\Controllers\PasswordController;
use App\Controllers\UserDashboardController;
use App\Controllers\AdminDashboardController;
use App\Controllers\UserRoomController;
use App\Controllers\UserBookingController;
use App\Controllers\AdminBookingController;
use App\Controllers\UserFeedbackController;
use App\Controllers\AdminUserController;
use App\Controllers\AdminRoomController;
use App\Controllers\AdminFeedbackController;
use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\AdminMiddleware;
use App\Core\Middleware\CsrfMiddleware;
use App\Core\Middleware\GuestMiddleware;
use App\Controllers\AdminReportController;


// Auth routes (public)
$app->router->get('/', [AuthController::class, 'login'], ['middleware' => [new GuestMiddleware()]]);
$app->router->get('/login', [AuthController::class, 'login'], ['middleware' => [new GuestMiddleware()]]);
$app->router->post('/login', [AuthController::class, 'login'], ['middleware' => [new GuestMiddleware(), new CsrfMiddleware()]]);
$app->router->get('/register', [AuthController::class, 'register'], ['middleware' => [new GuestMiddleware()]]);
$app->router->get('/register/mahasiswa', [AuthController::class, 'registerMahasiswa'], ['middleware' => [new GuestMiddleware()]]);
$app->router->post('/register/mahasiswa', [AuthController::class, 'registerMahasiswa'], ['middleware' => [new GuestMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/register/mahasiswa/validate-step1', [AuthController::class, 'validateMahasiswaStep1'], ['middleware' => [new GuestMiddleware()]]);
$app->router->post('/register/dosen/validate-step1', [AuthController::class, 'validateDosenStep1'], ['middleware' => [new GuestMiddleware()]]);
$app->router->get('/register/dosen', [AuthController::class, 'registerDosen'], ['middleware' => [new GuestMiddleware()]]);
$app->router->post('/register/dosen', [AuthController::class, 'registerDosen'], ['middleware' => [new GuestMiddleware(), new CsrfMiddleware()]]);
$app->router->get('/verify', [VerifyController::class, 'verify']);
$app->router->post('/verify', [VerifyController::class, 'verify'], ['middleware' => [new CsrfMiddleware()]]);
$app->router->post('/resend', [VerifyController::class, 'resend'], ['middleware' => [new CsrfMiddleware()]]);
$app->router->get('/forgot', [PasswordController::class, 'forgot']);
$app->router->post('/forgot', [PasswordController::class, 'forgot'], ['middleware' => [new CsrfMiddleware()]]);
$app->router->get('/reset-password', [PasswordController::class, 'reset']);
$app->router->post('/reset-password', [PasswordController::class, 'reset'], ['middleware' => [new CsrfMiddleware()]]);

// User routes (authenticated)
$app->router->get('/dashboard', [UserDashboardController::class, 'index'], ['middleware' => [new AuthMiddleware()]]);
$app->router->get('/profile', [ProfileController::class, 'index'], ['middleware' => [new AuthMiddleware()]]);
$app->router->post('/upload-kubaca', [ProfileController::class, 'uploadKubaca'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->get('/profile/detail', [ProfileController::class, 'detail'], ['middleware' => [new AuthMiddleware()]]);
$app->router->get('/profile/reset-password', [ProfileController::class, 'resetPassword'], ['middleware' => [new AuthMiddleware()]]);
$app->router->get('/profile/faq', [ProfileController::class, 'faq'], ['middleware' => [new AuthMiddleware()]]);
$app->router->get('/profile/verifikasi', [ProfileController::class, 'verifikasi'], ['middleware' => [new AuthMiddleware()]]);
$app->router->post('/logout', [AuthController::class, 'logout'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);

// Room & booking routes (authenticated)
$app->router->get('/rooms', [UserRoomController::class, 'index'], ['middleware' => [new AuthMiddleware()]]);
$app->router->get('/rooms/show', [UserRoomController::class, 'show'], ['middleware' => [new AuthMiddleware()]]);
$app->router->get('/bookings/draft', [UserBookingController::class, 'showDraft'], ['middleware' => [new AuthMiddleware()]]);
$app->router->post('/bookings/draft', [UserBookingController::class, 'createDraft'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/bookings/submit', [UserBookingController::class, 'submitDraft'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->get('/bookings/join', [UserBookingController::class, 'showJoinForm'], ['middleware' => [new AuthMiddleware()]]);
$app->router->post('/bookings/join', [UserBookingController::class, 'joinByLink'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->get('/my-bookings', [UserBookingController::class, 'showMyBooking'], ['middleware' => [new AuthMiddleware()]]);
$app->router->post('/bookings/cancel', [UserBookingController::class, 'cancelBooking'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/bookings/leave', [UserBookingController::class, 'leaveBooking'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/bookings/kick', [UserBookingController::class, 'kickMember'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->get('/bookings/detail', [UserBookingController::class, 'detail'], ['middleware' => [new AuthMiddleware()]]);
$app->router->get('/bookings/reschedule', [UserBookingController::class, 'showRescheduleForm'], ['middleware' => [new AuthMiddleware()]]);
$app->router->post('/bookings/reschedule/confirm', [UserBookingController::class, 'confirmReschedule'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/bookings/reschedule', [UserBookingController::class, 'reschedule'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/bookings/delete-draft', [UserBookingController::class, 'deleteDraft'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->get('/bookings/edit-draft', [UserBookingController::class, 'showEditDraft'], ['middleware' => [new AuthMiddleware()]]);
$app->router->post('/bookings/update-draft', [UserBookingController::class, 'updateDraft'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/bookings/cancel-pending', [UserBookingController::class, 'cancelPending'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);

// Invitation routes
$app->router->post('/invitations/send', [UserBookingController::class, 'send'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/invitations/accept', [UserBookingController::class, 'accept'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/invitations/reject', [UserBookingController::class, 'reject'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/invitations/cancel', [UserBookingController::class, 'cancel'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/invitations/approve', [UserBookingController::class, 'approveJoinRequest'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/invitations/reject-request', [UserBookingController::class, 'rejectJoinRequest'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/invitations/cancel-request', [UserBookingController::class, 'cancelJoinRequest'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);

// Feedback routes (authenticated)
$app->router->get('/feedback/create', [UserFeedbackController::class, 'create'], ['middleware' => [new AuthMiddleware()]]);
$app->router->post('/feedback/store', [UserFeedbackController::class, 'store'], ['middleware' => [new AuthMiddleware(), new CsrfMiddleware()]]);

// Admin routes
$app->router->get('/admin', [AdminDashboardController::class, 'index'], ['middleware' => [new AdminMiddleware()]]);
$app->router->get('/admin/settings', [AdminDashboardController::class, 'settings'], ['middleware' => [new AdminMiddleware()]]);

// Admin bookings
$app->router->get('/admin/bookings', [AdminBookingController::class, 'index'], ['middleware' => [new AdminMiddleware()]]);
$app->router->get('/admin/bookings/create', [AdminBookingController::class, 'create'], ['middleware' => [new AdminMiddleware()]]);
$app->router->post('/admin/bookings/store', [AdminBookingController::class, 'store'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->get('/admin/bookings/edit', [AdminBookingController::class, 'edit'], ['middleware' => [new AdminMiddleware()]]);
$app->router->post('/admin/bookings/update', [AdminBookingController::class, 'update'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/bookings/delete', [AdminBookingController::class, 'delete'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->get('/admin/bookings/detail', [AdminBookingController::class, 'detail'], ['middleware' => [new AdminMiddleware()]]);
$app->router->post('/admin/bookings/verify', [AdminBookingController::class, 'verify'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/bookings/complete', [AdminBookingController::class, 'complete'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/bookings/activate', [AdminBookingController::class, 'activate'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/bookings/cancel', [AdminBookingController::class, 'cancel'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/bookings/reject', [AdminBookingController::class, 'reject'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/bookings/noshow', [AdminBookingController::class, 'noshow'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->get('/admin/blocked-dates', [AdminBookingController::class, 'blockedDates'], ['middleware' => [new AdminMiddleware()]]);
$app->router->post('/admin/blocked-dates', [AdminBookingController::class, 'blockDate'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/blocked-dates/delete', [AdminBookingController::class, 'unblockDate'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/bookings/add', [AdminBookingController::class, 'addMember'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/bookings/kick', [AdminBookingController::class, 'kickMember'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->get('/admin/bookings/reschedule', [AdminBookingController::class, 'showRescheduleForm'], ['middleware' => [new AdminMiddleware()]]);
$app->router->post('/admin/bookings/reschedule', [AdminBookingController::class, 'reschedule'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->get('/admin/reports', [AdminReportController::class, 'index'], ['middleware' => [new AdminMiddleware()]]);

// Admin users
$app->router->get('/admin/users', [AdminUserController::class, 'index'], ['middleware' => [new AdminMiddleware()]]);
$app->router->get('/admin/users/create', [AdminUserController::class, 'create'], ['middleware' => [new AdminMiddleware()]]);
$app->router->post('/admin/users', [AdminUserController::class, 'store'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->get('/admin/users/edit', [AdminUserController::class, 'edit'], ['middleware' => [new AdminMiddleware()]]);
$app->router->get('/admin/users/show', [AdminUserController::class, 'show'], ['middleware' => [new AdminMiddleware()]]);
$app->router->post('/admin/users/update', [AdminUserController::class, 'update'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/users/delete', [AdminUserController::class, 'delete'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/users/suspend', [AdminUserController::class, 'suspend'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/users/unsuspend', [AdminUserController::class, 'unsuspend'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/users/reset-password', [AdminUserController::class, 'resetPassword'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/users/approve-kubaca', [AdminUserController::class, 'approveKubaca'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/users/reject-kubaca', [AdminUserController::class, 'rejectKubaca'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);

// Admin rooms
$app->router->get('/admin/rooms', [AdminRoomController::class, 'index'], ['middleware' => [new AdminMiddleware()]]);
$app->router->get('/admin/rooms/create', [AdminRoomController::class, 'create'], ['middleware' => [new AdminMiddleware()]]);
$app->router->post('/admin/rooms', [AdminRoomController::class, 'store'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->get('/admin/rooms/edit', [AdminRoomController::class, 'edit'], ['middleware' => [new AdminMiddleware()]]);
$app->router->get('/admin/rooms/show', [AdminRoomController::class, 'show'], ['middleware' => [new AdminMiddleware()]]);
$app->router->post('/admin/rooms/update', [AdminRoomController::class, 'update'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/rooms/delete', [AdminRoomController::class, 'delete'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/rooms/activate', [AdminRoomController::class, 'activate'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/rooms/deactivate', [AdminRoomController::class, 'deactivate'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/rooms/admin-only', [AdminRoomController::class, 'setAdminOnly'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/rooms/activate-all', [AdminRoomController::class, 'activateAll'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);
$app->router->post('/admin/rooms/deactivate-all', [AdminRoomController::class, 'deactivateAll'], ['middleware' => [new AdminMiddleware(), new CsrfMiddleware()]]);

// Admin feedback
$app->router->get('/admin/feedback', [AdminFeedbackController::class, 'index'], ['middleware' => [new AdminMiddleware()]]);
$app->router->get('/admin/feedback/detail', [AdminFeedbackController::class, 'detail'], ['middleware' => [new AdminMiddleware()]]);
$app->router->get('/admin/reports', [AdminReportController::class, 'index'], ['middleware' => [new AdminMiddleware()]]);