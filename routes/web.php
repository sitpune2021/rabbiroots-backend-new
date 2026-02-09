<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CustomerController;

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::resource('category', CategoryController::class);
    Route::resource('product', ProductController::class);
    Route::resource('brand', BrandController::class);
    Route::resource('role', RoleController::class);
    Route::resource('driver', DriverController::class);
    Route::resource('promo', PromoCodeController::class);
    Route::resource('store', StoreController::class);
    Route::resource('customer', CustomerController::class);

    Route::post('/category/update-feature', [CategoryController::class, 'updateFeature'])->name('category.update-feature');

 });