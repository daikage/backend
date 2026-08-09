<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/social-login', [AuthController::class, 'socialLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Payments
    Route::post('/payment/initialize', [\App\Http\Controllers\Api\PaymentController::class, 'initialize']);
    Route::post('/payment/verify/{gateway}', [\App\Http\Controllers\Api\PaymentController::class, 'verify']);

    // Rides
    Route::get('/rides/active', [\App\Http\Controllers\Api\RideController::class, 'active']);
    Route::post('/rides', [\App\Http\Controllers\Api\RideController::class, 'requestRide']);
    Route::post('/rides/{ride}/accept', [\App\Http\Controllers\Api\RideController::class, 'acceptRide']);
    Route::post('/rides/{ride}/status', [\App\Http\Controllers\Api\RideController::class, 'updateStatus']);
    Route::post('/rides/{ride}/location', [\App\Http\Controllers\Api\RideController::class, 'updateLocation']);
    Route::post('/rides/{ride}/rate', [\App\Http\Controllers\Api\RatingController::class, 'rate']);
    Route::get('/rides/{ride}', [\App\Http\Controllers\Api\RideController::class, 'show']);
    Route::get('/rides/available', [\App\Http\Controllers\Api\RideController::class, 'available']);
    Route::get('/rides/history', [\App\Http\Controllers\Api\RideController::class, 'history']);
    Route::get('/rides/{ride}', [\App\Http\Controllers\Api\RideController::class, 'show']);

    // Driver availability
    Route::post('/driver/availability', [AuthController::class, 'toggleAvailability']);
    
    // Wallets and Earnings
    Route::get('/driver/wallet', [\App\Http\Controllers\Api\EarningController::class, 'wallet']);
    Route::get('/driver/earnings', [\App\Http\Controllers\Api\EarningController::class, 'earnings']);
    
    // Chat
    Route::get('/rides/{ride}/messages', [\App\Http\Controllers\Api\ChatController::class, 'index']);
    Route::post('/rides/{ride}/messages', [\App\Http\Controllers\Api\ChatController::class, 'store']);
    
    // SOS
    Route::post('/rides/{ride}/sos', [\App\Http\Controllers\Api\SosController::class, 'store']);
    
    // Driver KYC Documents
    Route::get('/driver/documents', [\App\Http\Controllers\Api\DocumentController::class, 'show']);
    Route::post('/driver/documents', [\App\Http\Controllers\Api\DocumentController::class, 'upload']);
});

// Public Config Routes
Route::get('/ride-categories', [\App\Http\Controllers\Api\RideCategoryController::class, 'index']);
