<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{CategoryController, LandingController, DeliveryAgentController};



// Route::middleware(['auth:api', 'throttle:api'])->group(function () {
// });

Route::apiResource('delivery_agent', DeliveryAgentController::class);
Route::get('/landing', [LandingController::class, 'index']);