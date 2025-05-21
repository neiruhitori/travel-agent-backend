<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\API\DestinationController;
use App\Http\Controllers\API\PackageController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\VehicleController;
use App\Http\Controllers\API\PengajuanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);
Route::get('/who', [AuthController::class, 'who'])->middleware(['auth:sanctum']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('destinations', DestinationController::class);
    Route::apiResource('packages', PackageController::class);
    Route::apiResource('vehicles', VehicleController::class);
    Route::apiResource('bookings', BookingController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::apiResource('reviews', ReviewController::class);
    Route::apiResource('transactions', TransactionController::class);

});

Route::middleware(['auth:sanctum'])->group(function () {
    // Route::get('bookings', [BookingController::class, 'index']);
    Route::get('bookings/{booking}', [BookingController::class, 'show']);
    Route::post('bookings', [BookingController::class, 'store']);
    Route::put('bookings/{booking}', [BookingController::class, 'update']);
    Route::delete('bookings/{booking}', [BookingController::class, 'destroy']);

    // Route::get('payments', [PaymentController::class, 'index']);
    Route::get('payments/{payment}', [PaymentController::class, 'show']);
    Route::post('payments', [PaymentController::class, 'store']);

    // Route::get('reviews', [ReviewController::class, 'index']);
    Route::get('reviews/{review}', [ReviewController::class, 'show']);
    Route::post('reviews', [ReviewController::class, 'store']); // Customer boleh buat review

    // Route::get('transactions', [TransactionController::class, 'index']);
    Route::get('transactions/{transaction}', [TransactionController::class, 'show']);

     // Customer pengajuan endpoints
     Route::get('pengajuan/{pengajuan}', [PengajuanController::class, 'show']);
     Route::put('pengajuan/{pengajuan}', [PengajuanController::class, 'update']);
     Route::delete('pengajuan/{pengajuan}', [PengajuanController::class, 'destroy']);
     Route::post('pengajuan', [PengajuanController::class, 'store']);
});

// // Endpoint untuk User
// Route::apiResource('users', UserController::class);

// // Endpoint untuk Tujuan
// Route::apiResource('destinations', DestinationController::class);

// // Endpoint untuk Paket Perjalanan
// Route::apiResource('packages', PackageController::class);

// // Endpoint untuk Booking
// Route::apiResource('bookings', BookingController::class);

// // Endpoint untuk Pembayaran
// Route::apiResource('payments', PaymentController::class);

// // Endpoint untuk Ulasan
// Route::apiResource('reviews', ReviewController::class);

// // Endpoint untuk Transaksi
// Route::apiResource('transactions', TransactionController::class);

// // Endpoint untuk Travel
// Route::apiResource('vehicles', VehicleController::class);
