<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\API\DestinationController;
use App\Http\Controllers\API\PackageController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);
Route::get('/who', [AuthController::class, 'who'])->middleware(['auth:sanctum']);

// Endpoint untuk Paket User
Route::apiResource('users', UserController::class)->middleware(['auth:sanctum']);

// Endpoint untuk Paket Tujuan
Route::apiResource('destinations', DestinationController::class)->middleware(['auth:sanctum']);

// Endpoint untuk Paket Perjalanan
Route::apiResource('packages', PackageController::class)->middleware(['auth:sanctum']);

// Endpoint untuk Paket Booking
Route::apiResource('bookings', BookingController::class)->middleware(['auth:sanctum']);

// Endpoint untuk Pembayaran
Route::apiResource('payments', PaymentController::class)->middleware(['auth:sanctum']);

// Endpoint untuk Pembayaran
Route::apiResource('reviews', ReviewController::class)->middleware(['auth:sanctum']);

// Endpoint untuk Transaksi
Route::apiResource('transactions', TransactionController::class)->middleware(['auth:sanctum']);

