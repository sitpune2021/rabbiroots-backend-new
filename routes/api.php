<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{CategoryController, DashboardController, LandingController, DeliveryAgentController, OrderController, ProductController,RatingController};
use App\Http\Controllers\Api\Auth\DriverAuthController;
use App\Services\OrderAssignmentService;

// Route::middleware(['auth:api', 'throttle:api'])->group(function () {
// });

Route::apiResource('delivery_agent', DeliveryAgentController::class);
Route::get('/landing', [LandingController::class, 'index']);
Route::get('/listing', [LandingController::class, 'listing']);
Route::post('register-driver', [DriverAuthController::class, 'store']);
Route::post('/send-otp', [DriverAuthController::class, 'sendOtp']);
Route::post('/verify-otp', [DriverAuthController::class, 'verifyOtp']);
Route::post('/logout', [DriverAuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/forgot-password', [DriverAuthController::class, 'forgotPassword']);
Route::get('/product/{id}', [ProductController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/agent/profile', [DeliveryAgentController::class, 'profile']);

    Route::get('/orders/dashboard-count', [DashboardController::class, 'agentOrderCount']);

    Route::post('/agent/go-online', [DeliveryAgentController::class, 'goOnline']);
    Route::post('/agent/go-offline', [DeliveryAgentController::class, 'goOffline']);

    Route::post('/agent/update-location', [DeliveryAgentController::class, 'updateLocation']);

    Route::apiResource('drivers', DeliveryAgentController::class);
    Route::post('/agent/order/complete', [OrderController::class, 'completeOrder']);

    Route::post('/orders/{id}/assign', [OrderController::class, 'assign']);
    Route::post('/agent/accept-order', [DeliveryAgentController::class, 'acceptOrder']);
    Route::post('/agent/reject-order', [DeliveryAgentController::class, 'rejectOrder']);
    Route::post('/agent/update-order-status', [DeliveryAgentController::class, 'updateOrderStatus']);    // delivery delivered to customer

    Route::post('/agent/update-battery', [DeliveryAgentController::class, 'updateBattery']);
    Route::post('/agent/order/complete', [OrderController::class, 'completeOrder']);
    Route::post('/agent/heartbeat', [OrderController::class, 'heartbeat']);


    // Customer gives rating
    Route::post('/review/give', [RatingController::class, 'giveReview']);

    // Agent views their ratings
    Route::get('/agent/reviews', [RatingController::class, 'agentReviews']);
});
