<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::middleware('throttle:auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/social-login', [AuthController::class, 'socialLogin']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::middleware('auth:sanctum')->group(function () {
    // User / Profile
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Payments
    Route::post('/payment/initialize', [\App\Http\Controllers\Api\PaymentController::class, 'initialize']);
    Route::post('/payment/topup', [\App\Http\Controllers\Api\PaymentController::class, 'topup']);

    // Rides — static paths MUST come before the {ride} wildcard
    Route::get('/rides/active', [\App\Http\Controllers\Api\RideController::class, 'active']);
    Route::get('/rides/available', [\App\Http\Controllers\Api\RideController::class, 'available']);
    Route::get('/rides/history', [\App\Http\Controllers\Api\RideController::class, 'history']);
    Route::post('/rides/estimate', [\App\Http\Controllers\Api\RideController::class, 'estimate']);
    Route::post('/rides', [\App\Http\Controllers\Api\RideController::class, 'requestRide']);
    Route::post('/rides/{ride}/accept', [\App\Http\Controllers\Api\RideController::class, 'acceptRide']);
    Route::post('/rides/{ride}/cancel', [\App\Http\Controllers\Api\RideController::class, 'cancelRide']);
    Route::post('/rides/{ride}/status', [\App\Http\Controllers\Api\RideController::class, 'updateStatus']);
    Route::post('/rides/{ride}/location', [\App\Http\Controllers\Api\RideController::class, 'updateLocation']);
    Route::post('/rides/{ride}/customer-location', [\App\Http\Controllers\Api\RideController::class, 'updateCustomerLocation']);
    Route::post('/rides/{ride}/rate', [\App\Http\Controllers\Api\RatingController::class, 'rate']);
    Route::get('/rides/{ride}', [\App\Http\Controllers\Api\RideController::class, 'show']);

    // Driver availability
    Route::post('/driver/availability', [AuthController::class, 'toggleAvailability']);

    // Wallets and Earnings
    Route::get('/driver/wallet', [\App\Http\Controllers\Api\EarningController::class, 'wallet']);
    Route::get('/driver/earnings', [\App\Http\Controllers\Api\EarningController::class, 'earnings']);

    // Customer Wallet & Transactions
    Route::get('/customer/wallet', [\App\Http\Controllers\Api\WalletController::class, 'wallet']);
    Route::get('/customer/transactions', [\App\Http\Controllers\Api\WalletController::class, 'transactions']);

    // Chat
    Route::get('/rides/{ride}/messages', [\App\Http\Controllers\Api\ChatController::class, 'index']);
    Route::post('/rides/{ride}/messages', [\App\Http\Controllers\Api\ChatController::class, 'store']);

    // SOS
    Route::post('/rides/{ride}/sos', [\App\Http\Controllers\Api\SosController::class, 'store']);

    // Driver KYC Documents
    Route::get('/driver/documents', [\App\Http\Controllers\Api\DocumentController::class, 'show']);
    Route::post('/driver/documents', [\App\Http\Controllers\Api\DocumentController::class, 'upload']);

    // Vehicle Management
    Route::get('/vehicles', [\App\Http\Controllers\Api\VehicleController::class, 'index']);
    Route::post('/vehicles', [\App\Http\Controllers\Api\VehicleController::class, 'store']);
    Route::put('/vehicles/{vehicle}', [\App\Http\Controllers\Api\VehicleController::class, 'update']);
    Route::delete('/vehicles/{vehicle}', [\App\Http\Controllers\Api\VehicleController::class, 'destroy']);
});

// Reverb/Pusher broadcasting auth route for Sanctum
use Illuminate\Support\Facades\Broadcast;
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// Public Config Routes
Route::get('/ride-categories', [\App\Http\Controllers\Api\RideCategoryController::class, 'index']);

// Payment Gateway Webhooks (no auth — validated by signature)
Route::post('/webhooks/paystack', [\App\Http\Controllers\Api\PaymentController::class, 'paystackWebhook']);
Route::post('/webhooks/flutterwave', [\App\Http\Controllers\Api\PaymentController::class, 'flutterwaveWebhook']);

// Payment verification callback (public — Paystack redirects the customer's browser here
// after payment with no Bearer token, so it must NOT require auth). The verify() method
// only looks up the transaction by reference and confirms it with the gateway API.
Route::match(['get', 'post'], '/payment/verify/{gateway}', [\App\Http\Controllers\Api\PaymentController::class, 'verify']);
