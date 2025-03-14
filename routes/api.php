<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RentalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// auth
Route::prefix('auth')
    ->as('auth.')
    ->group(
        function () {
            Route::post('/register', [AuthController::class, 'register'])->name('register');
            Route::post('/login', [AuthController::class, 'login'])->name('login');
            Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout'])->name('logout');
        }
    );

Route::apiResource('cars', CarController::class)->only(['index', 'show']);

Route::prefix('cars')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [CarController::class, 'store']);
    Route::put('/{car}', [CarController::class, 'update']);
    Route::delete('/{car}', [CarController::class, 'destroy']);
});

Route::get('cars/page/{page}', [CarController::class,'paginate']);

Route::apiResource('rentals', RentalController::class)->only(['index', 'show']);

Route::prefix('rentals')->middleware('auth:sanctum')->group(function () {
    Route::post('/', [RentalController::class, 'store']);
    Route::put('/{rental}', [RentalController::class, 'update']);
    Route::delete('/{rental}', [RentalController::class, 'destroy']);
});

Route::get('users/{userId}/rentals', [RentalController::class, 'rentalsByUser']);
Route::get('cars/{carId}/rentals', [RentalController::class, 'rentalsByCar']);


Route::apiResource('payments', PaymentController::class);

Route::get('checkout/success', [PaymentController::class, 'success'])->name('checkout.success');
Route::get('checkout/cancel', [PaymentController::class, 'cancel'])->name('checkout.cancel');