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
use App\Controllers\CheckInController;
use App\Controllers\FeedbackController;
use App\Models\User;

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
$app->router->get('/rooms/show', [UserRoomController::class, 'show']);
$app->router->get('/bookings/draft', [UserBookingController::class, 'showDraft']);
$app->router->post('/bookings/draft', [UserBookingController::class, 'createDraft']);
$app->router->post('/bookings/submit', [UserBookingController::class, 'submitDraft']);
$app->router->post('/bookings/member', [UserBookingController::class, 'addMember']);
$app->router->get('/feedback/create', [FeedbackController::class, 'create']);
$app->router->post('/feedback', [FeedbackController::class, 'store']);

// Check-in routes
$app->router->get('/checkin', [CheckInController::class, 'index']);
$app->router->post('/checkin', [CheckInController::class, 'verify']);

// Admin routes
$app->router->get('/admin', [AdminDashboardController::class, 'index']);
$app->router->get('/admin/bookings', [AdminBookingController::class, 'index']);
$app->router->post('/admin/bookings/verify', [AdminBookingController::class, 'verify']);
$app->router->post('/admin/bookings/complete', [AdminBookingController::class, 'complete']);
