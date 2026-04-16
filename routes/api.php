<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{AccountDeletionController, CategoryController, CustomerAddressController, DashboardController, LandingController, DeliveryAgentController, GofrugalController, OrderController, ProductController, RatingController, ReviewController, WishlistController};
use App\Http\Controllers\Api\Auth\CustomerAuthController;
use App\Http\Controllers\Api\Auth\DriverAuthController;
use App\Http\Controllers\Api\Customer\CartController;
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
Route::post('/resend-otp', [DriverAuthController::class, 'resendOtp']);
Route::get('/product/{id}', [ProductController::class, 'show']);
Route::get('/categories/subcategories/{id}', [CategoryController::class, 'getSubCategories']);
Route::get('/category-products/{id}', [ProductController::class, 'getByCategory']);
Route::get('/brands/{id}/products', [ProductController::class, 'getByBrand']);
Route::get('/categories/{id}/brands', [ProductController::class, 'getBrandsByCategory']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/orders/dashboard-count', [DashboardController::class, 'agentOrderCount']);

    Route::apiResource('drivers', DeliveryAgentController::class);
    Route::post('/agent/go-online-offline', [DeliveryAgentController::class, 'updateOnlineStatus']);
    Route::post('/agent/update-location', [DeliveryAgentController::class, 'updateLocation']);
    Route::post('/agent/accept-order', [DeliveryAgentController::class, 'acceptOrder']);
    Route::post('/agent/reject-order', [DeliveryAgentController::class, 'rejectOrder']);
    Route::post('/agent/update-order-status', [DeliveryAgentController::class, 'updateOrderStatus']);    // delivery delivered to customer
    Route::post('/agent/update-battery', [DeliveryAgentController::class, 'updateBattery']);
    Route::get('/agent/profile', [DeliveryAgentController::class, 'profile']);

    //  Start Delivery Attempt API- When agent reaches location.
    Route::post('/agent/start-delivery/{order}', [DeliveryAgentController::class, 'startAttempt']);
    // Call Attempt API- When agent clicks Call Primary / Secondary
    Route::post('/agent/call/{order}', [DeliveryAgentController::class, 'callCustomer']);
    // Completed Orders API
    Route::get('/orders/completed/{id?}', [OrderController::class, 'getCompletedOrders']);
    // Picked Orders API
    Route::get('/orders/picked/{id?}', [OrderController::class, 'getPickedOrders']);

    Route::post('/agent/order/complete', [OrderController::class, 'completeOrder']);
    Route::post('/orders/{id}/assign', [OrderController::class, 'assign']);
    Route::post('/agent/order/complete', [OrderController::class, 'completeOrder']);
    Route::post('/agent/heartbeat', [OrderController::class, 'heartbeat']);

    // Get All New Orders api
    Route::get('/orders/new/{id?}', [OrderController::class, 'getNewOrders']);
    // Get All cancelled Orders api
    Route::get('/orders/cancelled/{id?}', [OrderController::class, 'getCancelledOrders']);

    // Customer gives rating
    Route::post('/review/give', [ReviewController::class, 'giveReview']);
    // Agent views their ratings
    Route::get('/agent/reviews', [ReviewController::class, 'agentReviews']);


    //  Secondary Contact Number - Fallback Communication
    Route::post('/agent/start-delivery/{order}', [DeliveryAgentController::class, 'startAttempt']);

    Route::post('/agent/call-customer/{order}', [DeliveryAgentController::class, 'callCustomer']);

    Route::post('/agent/customer-answered/{order}', [DeliveryAgentController::class, 'customerAnswered']);
});

Route::prefix('customer')->group(function () {

    Route::post('send-otp', [CustomerAuthController::class, 'sendOtp']);
    Route::post('verify-otp', [CustomerAuthController::class, 'verifyOtp']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/add-address', [CustomerAddressController::class, 'addAddress']);
        Route::get('/address-list', [CustomerAddressController::class, 'getAddresses']);
        Route::post('/update-address/{id}', [CustomerAddressController::class, 'updateAddress']);
        Route::delete('/delete-address/{id}', [CustomerAddressController::class, 'deleteAddress']);
        Route::post('/address/set-default/{id}', [CustomerAddressController::class, 'setDefault']);

        // Add to cart
        Route::post('/add-to-cart', [CartController::class, 'addToCart']);
        Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity']);
        Route::get('view-cart', [CartController::class, 'viewCart']);

        // Account deletion
        // Route::middleware('auth:sanctum')->group(function () {
            Route::post('/account/delete-request', [AccountDeletionController::class, 'requestDeletion']);
            Route::post('/account/send-otp', [AccountDeletionController::class, 'sendDeleteOtp']);
            Route::post('/account/verify-otp', [AccountDeletionController::class, 'verifyDeleteOtp']);
        // });
    });

    Route::get('/products/{id}/similar', [ProductController::class, 'getSimilarProducts']);
});

// Route::middleware('auth:sanctum')->group(function () {

//     Route::post('/wishlist/add', [WishlistController::class, 'add']);

//     Route::get('/wishlist', [WishlistController::class, 'list']);

//     Route::delete('/wishlist/remove/{product_id}', [WishlistController::class, 'remove']);
// });

Route::get('/gofrugal/items', [GofrugalController::class, 'getItems']);
