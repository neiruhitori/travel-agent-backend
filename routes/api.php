<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\API\DestinationController;
use App\Http\Controllers\API\PackageController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Endpoint untuk Paket User
Route::apiResource('users', UserController::class);

// Endpoint untuk Paket Tujuan
Route::apiResource('destinations', DestinationController::class);

// Endpoint untuk Paket Perjalanan
Route::apiResource('packages', PackageController::class);

// Endpoint untuk Paket Booking
Route::apiResource('bookings', BookingController::class);

// Endpoint untuk Pembayaran
Route::apiResource('payments', PaymentController::class);
