<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{CategoryController};



// Route::middleware(['auth:api', 'throttle:api'])->group(function () {
    Route::apiResource('categories', CategoryController::class);
// });
