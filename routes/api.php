<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ClothingTypeController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\MeasurementHistoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductionProgressController;
use App\Http\Controllers\Api\TailorAssignmentController;
use App\Http\Controllers\Api\TailorController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//Auth Routes
Route::post('/login', LoginController::class);
Route::post('/logout', LogoutController::class)->middleware('auth:sanctum');

// Guest API Routes for Password Reset
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

Route::get('/email/verify/{id}/{hash}', [UserController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/email/verification-notification', [UserController::class, 'resend'])
        ->middleware('throttle:6,1');
    Route::middleware('verified')->group(function () {
		//User Routes
		Route::get('/users', [UserController::class, 'index']);
		Route::get('/users/{id}', [UserController::class, 'show']);
		Route::post('/users/register', [UserController::class, 'store']);
		Route::post('/users/delete/{id}', [UserController::class, 'destroy']);
		Route::post('/users/{id}/edit', [UserController::class, 'update']);
		Route::get('/user', function (Request $request) {
				return $request->user();
		});
        Route::post('/users/change-password', [UserController::class, 'changePassword']);
		
        //Customuer Routes
        Route::get('/customers', [CustomerController::class, 'index']);
        Route::get('/customers/{id}', [CustomerController::class, 'show']);
        Route::post('/customers', [CustomerController::class, 'store']);
        Route::post('/customers/delete/{id}', [CustomerController::class, 'destroy']);
        Route::post('/customers/{id}/edit', [CustomerController::class, 'update']);

        //Tailor Routes
        Route::get('/tailors', [TailorController::class, 'index']);
        Route::get('/tailors/{id}', [TailorController::class, 'show']);
        Route::post('/tailors', [TailorController::class, 'store']);
        Route::post('/tailors/delete/{id}', [TailorController::class, 'destroy']);
        Route::post('/tailors/{id}/edit', [TailorController::class, 'update']);

        //Material Routes
        Route::get('/materials', [MaterialController::class, 'index']);
        Route::get('/materials/{id}', [MaterialController::class, 'show']);
        Route::post('/materials', [MaterialController::class, 'store']);
        Route::post('/materials/delete/{id}', [MaterialController::class, 'destroy']);
        Route::post('/materials/{id}/edit', [MaterialController::class, 'update']);

        //Clothing Type Routes
        Route::get('/clothing-types', [ClothingTypeController::class, 'index']);
        Route::get('/clothing-types/{id}', [ClothingTypeController::class, 'show']);
        Route::post('/clothing-types', [ClothingTypeController::class, 'store']);
        Route::post('/clothing-types/delete/{id}', [ClothingTypeController::class, 'destroy']);
        Route::post('/clothing-types/{id}/edit', [ClothingTypeController::class, 'update']);

        //Measurement History Routes
        Route::get('/measurement-histories', [MeasurementHistoryController::class, 'index']);
        Route::get('/measurement-histories/{id}', [MeasurementHistoryController::class, 'show']);
        Route::get('/getbycustomerandclothing', [MeasurementHistoryController::class, 'getByCustomerAndClothingType']);
        Route::post('/measurement-histories', [MeasurementHistoryController::class, 'store']);
        Route::post('/measurement-histories/delete/{id}', [MeasurementHistoryController::class, 'destroy']);
        Route::post('/measurement-histories/{id}/edit', [MeasurementHistoryController::class, 'update']);

        //Order Routes
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/search', [OrderController::class, 'getBySearch']);
        Route::get('/orders/unpaid', [OrderController::class, 'unpaid']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::post('/orders/delete/{id}', [OrderController::class, 'destroy']);
        Route::post('/orders/{id}/edit', [OrderController::class, 'update']);

        //Production Progress Routes
        Route::get('/production-progress/{id}', [ProductionProgressController::class, 'show']);
        Route::post('/production-progress', [ProductionProgressController::class, 'store']);
        Route::post('/production-progress/delete/{id}', [ProductionProgressController::class, 'destroy']);
        Route::post('/production-progress/{id}/edit', [ProductionProgressController::class, 'update']);

        //Payment Routes
        Route::get('/payments', [PaymentController::class, 'index']);
        Route::get('/payments/{id}', [PaymentController::class, 'show']);
        Route::post('/payments', [PaymentController::class, 'store']);
        Route::post('/payments/delete/{id}', [PaymentController::class, 'destroy']);
        Route::post('/payments/{id}/edit', [PaymentController::class, 'update']);

        //Tailor Assignment Routes
        Route::get('/tailor-assignments', [TailorAssignmentController::class, 'index']);
        // Route::get('/tailor-assignments/{id}', [TailorAssignmentController::class, 'show']);
        Route::post('/tailor-assignments', [TailorAssignmentController::class, 'store']);
        // Route::post('/tailor-assignments/delete/{id}', [TailorAssignmentController::class, 'destroy']);
        // Route::post('/tailor-assignments/{id}/edit', [TailorAssignmentController::class, 'update']);
    });
});
