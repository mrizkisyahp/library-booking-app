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

// Auth routes
$app->router->get('/', [AuthController::class, 'login']);
$app->router->get('/login', [AuthController::class, 'login']);
$app->router->post('/login', [AuthController::class, 'login']);
$app->router->get('/register', [AuthController::class, 'register']);
$app->router->get('/register/mahasiswa', [AuthController::class, 'registerMahasiswa']);
$app->router->post('/register/mahasiswa', [AuthController::class, 'registerMahasiswa']);
$app->router->get('/register/dosen', [AuthController::class, 'registerDosen']);
$app->router->post('/register/dosen', [AuthController::class, 'registerDosen']);
$app->router->post('/logout', [AuthController::class, 'logout']);
$app->router->get('/verify', [VerifyController::class, 'verify']);
$app->router->post('/verify', [VerifyController::class, 'verify']);
$app->router->get('/resend', [VerifyController::class, 'resend']);
$app->router->get('/forgot', [PasswordController::class, 'forgot']);
$app->router->post('/forgot', [PasswordController::class, 'forgot']);
$app->router->get('/reset', [PasswordController::class, 'reset']);
$app->router->post('/reset', [PasswordController::class, 'reset']);

// User routes
$app->router->get('/dashboard', [UserDashboardController::class, 'index']);
$app->router->get('/profile', [ProfileController::class, 'index']);
$app->router->post('/upload-kubaca', [ProfileController::class, 'uploadKubaca']);

// Room & booking routes 
$app->router->get('/rooms', [UserRoomController::class, 'index']);
$app->router->get('/bookings/draft', [UserBookingController::class, 'showDraft']);
$app->router->post('/bookings/draft', [UserBookingController::class, 'createDraft']);
$app->router->post('/bookings/submit', [UserBookingController::class, 'submitDraft']);
$app->router->get('/rooms/show', [UserRoomController::class, 'show']);
$app->router->post('/bookings/member', [UserBookingController::class, 'addMember']);
$app->router->get('/feedback/create', [UserFeedbackController::class, 'create']);
$app->router->post('/feedback', [UserFeedbackController::class, 'store']);
$app->router->get('/bookings/join', [UserBookingController::class, 'showJoinForm']);
$app->router->post('/bookings/join', [UserBookingController::class, 'joinByLink']);

// Admin routes
$app->router->get('/admin', [AdminDashboardController::class, 'index']);
$app->router->get('/admin/bookings', [AdminBookingController::class, 'index']);
$app->router->get('/admin/bookings/detail', [AdminBookingController::class, 'detail']);
$app->router->post('/admin/bookings/verify', [AdminBookingController::class, 'verify']);
$app->router->post('/admin/bookings/complete', [AdminBookingController::class, 'complete']);
$app->router->post('/admin/bookings/activate', [AdminBookingController::class, 'activate']);
$app->router->post('/admin/bookings/cancel', [AdminBookingController::class, 'cancel']);
$app->router->get('/admin/users', [AdminUserController::class, 'index']);
$app->router->get('/admin/users/create', [AdminUserController::class, 'create']);
$app->router->post('/admin/users', [AdminUserController::class, 'store']);
$app->router->get('/admin/users/edit', [AdminUserController::class, 'edit']);
$app->router->get('/admin/users/show', [AdminUserController::class, 'show']);
$app->router->post('/admin/users/update', [AdminUserController::class, 'update']);
$app->router->post('/admin/users/delete', [AdminUserController::class, 'delete']);
$app->router->post('/admin/users/suspend', [AdminUserController::class, 'suspend']);
$app->router->post('/admin/users/unsuspend', [AdminUserController::class, 'unsuspend']);
$app->router->post('/admin/users/reset-password', [AdminUserController::class, 'resetPassword']);
$app->router->post('/admin/users/approve-kubaca', [AdminUserController::class, 'approveKubaca']);
$app->router->post('/admin/users/reject-kubaca', [AdminUserController::class, 'rejectKubaca']);
$app->router->get('/admin/rooms', [AdminRoomController::class, 'index']);
$app->router->get('/admin/rooms/create', [AdminRoomController::class, 'create']);
$app->router->post('/admin/rooms', [AdminRoomController::class, 'store']);
$app->router->get('/admin/rooms/edit', [AdminRoomController::class, 'edit']);
$app->router->get('/admin/rooms/show', [AdminRoomController::class, 'show']);
$app->router->post('/admin/rooms/update', [AdminRoomController::class, 'update']);
$app->router->post('/admin/rooms/delete', [AdminRoomController::class, 'delete']);
$app->router->post('/admin/rooms/activate', [AdminRoomController::class, 'activate']);
$app->router->post('/admin/rooms/deactivate', [AdminRoomController::class, 'deactivate']);
$app->router->get('/admin/bookings/detail', [AdminBookingController::class, 'detail']);
$app->router->get('/admin/feedback', [AdminFeedbackController::class, 'index']);
$app->router->get('/admin/feedback/detail', [AdminFeedbackController::class, 'detail']);
$app->router->get('/admin/bookings', [AdminBookingController::class, 'index']);
$app->router->get('/admin/bookings/create', [AdminBookingController::class, 'create']);
$app->router->post('/admin/bookings/store', [AdminBookingController::class, 'store']);
$app->router->get('/admin/bookings/edit', [AdminBookingController::class, 'edit']);
$app->router->post('/admin/bookings/update', [AdminBookingController::class, 'update']);
$app->router->post('/admin/bookings/delete', [AdminBookingController::class, 'delete']);