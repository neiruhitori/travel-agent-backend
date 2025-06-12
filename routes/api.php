<?php

// use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\BookingDestController;
use App\Http\Controllers\API\DestinationController;
use App\Http\Controllers\API\PackageController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PengajuanController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\VehicleController;
use App\Http\Controllers\API\InvoiceController;
use App\Http\Controllers\API\PaymentSubController;
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
    Route::apiResource('paymentsub', PaymentSubController::class);
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

    // New booking endpoint
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
});

// buat landing page
Route::apiResource('packages', PackageController::class);
Route::apiResource('bookings', BookingController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('vehicles', VehicleController::class);
Route::apiResource('destinations', DestinationController::class);
Route::apiResource('pengajuan', PengajuanController::class);

Route::apiResource('invoices', InvoiceController::class);
// resend Email
Route::post('invoices/{id}/resend', [InvoiceController::class, 'resendEmail']);
Route::post('/pengajuan/{id}/resend-payment-received', [PengajuanController::class, 'resendPaymentReceived']);

Route::get('invoice/by-pengajuan/{pengajuan_id}', [InvoiceController::class, 'byPengajuan']);

// Route untuk generate barcode pembayaran sub
Route::post('paymentsub/generate-barcode', [PaymentSubController::class, 'generateBarcode']);
// Route untuk mengambil barcode berdasarkan pengajuan_id
Route::get('paymentsub/barcode/{pengajuan_id}', [PaymentSubController::class, 'getBarcodeByPengajuan']);

// Route untuk update status pengajuan user
Route::patch('pengajuan/{id}/status', [PengajuanController::class, 'updateStatus']);
Route::apiResource('paymentsub', PaymentSubController::class);
Route::get('notifications/paymentsub', [PaymentSubController::class, 'notifications']);

Route::get('/paymentsub/status/{pengajuan_id}', [PaymentSubController::class, 'getPaymentStatus']);

Route::get('paymentsub/by-pengajuan/{pengajuanId}', [PaymentSubController::class, 'getByPengajuan']);
