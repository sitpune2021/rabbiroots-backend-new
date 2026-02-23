<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{CategoryController, LandingController, DeliveryAgentController, OrderController};
use App\Http\Controllers\Api\Auth\DriverAuthController;
use App\Services\OrderAssignmentService;

// Route::middleware(['auth:api', 'throttle:api'])->group(function () {
// });

Route::post('register-driver', [DriverAuthController::class, 'store']);
Route::post('/send-otp', [DriverAuthController::class, 'sendOtp']);
Route::post('/verify-otp', [DriverAuthController::class, 'verifyOtp']);
Route::post('/logout', [DriverAuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/forgot-password', [DriverAuthController::class, 'forgotPassword']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('/agent/go-online', [DeliveryAgentController::class, 'goOnline']);
    Route::post('/agent/go-offline', [DeliveryAgentController::class, 'goOffline']);

    Route::post('/agent/update-location', [DeliveryAgentController::class, 'updateLocation']);

    Route::apiResource('drivers', DeliveryAgentController::class);
    Route::post('/agent/order/complete', [OrderController::class, 'completeOrder']);

    // Route::get('/test-assign/{id}', function ($id) {

    //     $service = new OrderAssignmentService();
    //     $result = $service->assignOrder($id);

    //     return response()->json($result);
    // });

    Route::post('/orders/{id}/assign', [OrderController::class, 'assign']);
    Route::post('/agent/accept-order', [DeliveryAgentController::class, 'acceptOrder']);
    Route::post('/agent/reject-order', [DeliveryAgentController::class, 'rejectOrder']);
    Route::post('/agent/update-order-status', [DeliveryAgentController::class, 'updateOrderStatus']);    // delivery delivered to customer

    Route::post('/agent/update-battery', [DeliveryAgentController::class, 'updateBattery']);
    Route::post('/agent/order/complete', [OrderController::class, 'completeOrder']);
    Route::post('/agent/heartbeat', [OrderController::class, 'heartbeat']);

});

Route::get('/landing', [LandingController::class, 'index']);
