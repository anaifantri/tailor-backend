<?php

use App\Http\Controllers\Api\ClothingTypeController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\MeasurementHistoryController;
use App\Http\Controllers\Api\OrderCancellationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderDetailDeliveryController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductionProgressController;
use App\Http\Controllers\Api\TailorAssignmentController;
use App\Http\Controllers\Api\TailorController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::post('/login', LoginController::class)->name('login');
Route::post('/logout', LogoutController::class)->middleware('auth:sanctum');

// Guest API Routes for Password Reset
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

// Email Verification (Guest / Signed URL)
Route::get('/email/verify/{ulid}/{hash}', [UserController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/email/verification-notification', [UserController::class, 'resend'])
        ->middleware('throttle:6,1');

    Route::middleware('verified')->group(function () {
        // User Info & Password
        Route::get('/user', fn (Request $request) => $request->user());
        Route::post('/users/change-password', [UserController::class, 'changePassword']);

        /*
        |--------------------------------------------------------------------------
        | API Resources (Parameter ULID & RESTful Verbs)
        |--------------------------------------------------------------------------
        */

        Route::apiResource('users', UserController::class)->parameters(['users' => 'ulid']);
        Route::apiResource('customers', CustomerController::class)->parameters(['customers' => 'ulid']);
        Route::apiResource('tailors', TailorController::class)->parameters(['tailors' => 'ulid']);
        Route::apiResource('materials', MaterialController::class)->parameters(['materials' => 'ulid']);
        Route::apiResource('clothing-types', ClothingTypeController::class)->parameters(['clothing-types' => 'ulid']);

        // Custom Search Measurement Histories
        Route::get('/measurement-histories/by-customer-clothing', [MeasurementHistoryController::class, 'getByCustomerAndClothingType']);
        Route::apiResource('measurement-histories', MeasurementHistoryController::class)->parameters(['measurement-histories' => 'ulid']);

        // Custom Search Orders
        Route::get('/orders/search', [OrderController::class, 'getBySearch']);
        Route::get('/orders/unpaid', [OrderController::class, 'unpaid']);
        Route::apiResource('orders', OrderController::class)->parameters(['orders' => 'ulid']);

        Route::apiResource('production-progress', ProductionProgressController::class)->parameters(['production-progress' => 'ulid']);
        Route::apiResource('payments', PaymentController::class)->parameters(['payments' => 'ulid']);
        Route::apiResource('tailor-assignments', TailorAssignmentController::class)->parameters(['tailor-assignments' => 'ulid']);
        Route::apiResource('deliveries', OrderDetailDeliveryController::class)->parameters(['deliveries' => 'ulid']);

        // Custom Search Order Cancellations
        Route::get('/order-cancellations/order-detail/{orderDetailUlid}', [OrderCancellationController::class, 'getByOrderDetail']);
        Route::get('/order-cancellations/order/{orderUlid}', [OrderCancellationController::class, 'getByOrder']);
        Route::apiResource('order-cancellations', OrderCancellationController::class)->parameters(['order-cancellations' => 'ulid']);
    });
});