<?php

namespace App\Routes;

use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\VerifyController;
use App\Controllers\PasswordController;
use App\Controllers\UserDashboardController;
use App\Controllers\AdminDashboardController;
use App\Controllers\RoomController;
use App\Controllers\BookingController;
use App\Controllers\AdminRoomController;
use App\Controllers\AdminUserController;
use App\Controllers\AdminReportController;
use App\Controllers\AdminBookingController;
use App\Controllers\CheckInController;

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

// Room & booking routes (placeholder - not implemented yet)
$app->router->get('/rooms', [RoomController::class, 'index']);
$app->router->get('/room', [RoomController::class, 'view']);
$app->router->get('/book', [BookingController::class, 'create']);
$app->router->post('/book', [BookingController::class, 'create']);
$app->router->get('/my-bookings', [BookingController::class, 'myBookings']);

// Check-in routes
$app->router->get('/checkin', [CheckInController::class, 'index']);
$app->router->post('/checkin/verify', [CheckInController::class, 'verify']);
$app->router->post('/checkout', [CheckInController::class, 'checkout']);

// Admin routes
$app->router->get('/admin', [AdminDashboardController::class, 'index']);
$app->router->get('/admin/rooms', [AdminRoomController::class, 'index']);
$app->router->get('/admin/rooms/create', [AdminRoomController::class, 'create']);
$app->router->post('/admin/rooms/create', [AdminRoomController::class, 'create']);
$app->router->get('/admin/rooms/edit', [AdminRoomController::class, 'edit']);
$app->router->post('/admin/rooms/edit', [AdminRoomController::class, 'edit']);
$app->router->post('/admin/rooms/delete', [AdminRoomController::class, 'delete']);
$app->router->get('/admin/users', [AdminUserController::class, 'index']);
$app->router->post('/admin/users/status', [AdminUserController::class, 'updateStatus']);
$app->router->get('/admin/bookings', [AdminBookingController::class, 'index']);
$app->router->post('/admin/bookings/validate', [AdminBookingController::class, 'validate']);
$app->router->post('/admin/bookings/cancel', [AdminBookingController::class, 'cancel']);
$app->router->post('/admin/bookings/complete', [AdminBookingController::class, 'complete']);
$app->router->get('/admin/reports', [AdminReportController::class, 'index']);
$app->router->get('/admin/reports/generate', [AdminReportController::class, 'generate']);