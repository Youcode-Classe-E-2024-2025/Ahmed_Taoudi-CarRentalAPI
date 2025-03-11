<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarController;
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

Route::apiResource('cars', CarController::class);
