<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{CategoryController, DeliveryAgentController};



// Route::middleware(['auth:api', 'throttle:api'])->group(function () {
Route::apiResource('categories', CategoryController::class);
// });


// Route::middleware('auth')->group(function () {
Route::apiResource('delivery_agent', DeliveryAgentController::class);
// });
