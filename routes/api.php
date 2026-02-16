<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{CategoryController, LandingController};



// Route::middleware(['auth:api', 'throttle:api'])->group(function () {
// });
Route::get('/landing', [LandingController::class, 'index']);