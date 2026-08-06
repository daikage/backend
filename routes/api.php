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
    Route::post('/rides/request', [\App\Http\Controllers\Api\RideController::class, 'requestRide']);
    Route::post('/rides/{ride}/accept', [\App\Http\Controllers\Api\RideController::class, 'acceptRide']);
    Route::post('/rides/{ride}/status', [\App\Http\Controllers\Api\RideController::class, 'updateStatus']);
    Route::post('/rides/{ride}/location', [\App\Http\Controllers\Api\RideController::class, 'updateLocation']);
});
