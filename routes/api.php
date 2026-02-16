<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{CategoryController, LandingController, DeliveryAgentController};



// Route::middleware(['auth:api', 'throttle:api'])->group(function () {
// });


Route::middleware('auth:sanctum')->group(function () {
Route::post('/send-otp', [DeliveryAgentController::class, 'sendOtp']);
Route::post('/verify-otp', [DeliveryAgentController::class, 'verifyOtp']);


Route::post('/agent/go-online', [DeliveryAgentController::class, 'goOnline']);
Route::post('/agent/go-offline', [DeliveryAgentController::class, 'goOffline']);

Route::post('/agent/update-location', [DeliveryAgentController::class, 'updateLocation']);

Route::apiResource('delivery_agent', DeliveryAgentController::class);
});

Route::get('/landing', [LandingController::class, 'index']);
