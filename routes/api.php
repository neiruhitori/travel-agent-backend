<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\BookingDestController;
use App\Http\Controllers\API\DestinationController;
use App\Http\Controllers\API\PackageController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\API\PengajuanController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public authentication routes
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);
// Route::get('/who', [AuthController::class, 'who'])->middleware(['auth:sanctum']);

// Admin routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('destinations', DestinationController::class);
    Route::apiResource('packages', PackageController::class);
    Route::apiResource('vehicles', VehicleController::class);
    Route::apiResource('bookings', BookingController::class);
    Route::apiResource('bookingdes', BookingDestController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('reviews', ReviewController::class);
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('pengajuan', PengajuanController::class);
});

// Authenticated user routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Customer pengajuan endpoints
    Route::get('pengajuan/{pengajuan}', [PengajuanController::class, 'show']);
    Route::put('pengajuan/{pengajuan}', [PengajuanController::class, 'update']);
    Route::delete('pengajuan/{pengajuan}', [PengajuanController::class, 'destroy']);
    Route::post('pengajuan', [PengajuanController::class, 'store']);

    Route::apiResource('bookingdes', BookingDestController::class);

    Route::apiResource('packages', PackageController::class);
    Route::apiResource('bookings', BookingController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('vehicles', VehicleController::class);
    Route::apiResource('destinations', DestinationController::class);

    // Update profile
    Route::put('users/{user}', [UserController::class, 'update']);

    // Customer payments endpoints
    Route::get('payments/{payment}', [PaymentController::class, 'show']);
    Route::post('payments', [PaymentController::class, 'store']);

    // Customer reviews endpoints
    Route::get('reviews/{review}', [ReviewController::class, 'show']);
    Route::post('reviews', [ReviewController::class, 'store']);

    // Customer transactions endpoints
    Route::get('transactions/{transaction}', [TransactionController::class, 'show']);
});

// buat landing page
Route::apiResource('packages', PackageController::class);
Route::apiResource('bookings', BookingController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('vehicles', VehicleController::class);
Route::apiResource('destinations', DestinationController::class);
Route::apiResource('pengajuan', PengajuanController::class);
