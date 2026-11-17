<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', HealthController::class)->name('health');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('auth.login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

        Route::prefix('user')->group(function () {
            Route::get('/1', [UserController::class, 'index'])->name('user');
            Route::get('/{user}', [UserController::class, 'show']);
            Route::delete('/{user}', [UserController::class, 'destroy']);
            Route::post("/created", [UserController::class, 'store']);
        });

        Route::prefix('contact')->group(function () {
            Route::get('/', [ContactController::class, 'index'])->name('contact');
            Route::post('/', [ContactController::class, 'store']);
            Route::get('/{contact}', [ContactController::class, 'show']);
            Route::put('/{contact}', [ContactController::class, 'update']);
            Route::delete('/{contact}', [ContactController::class, 'destroy']);
        });

    });

});
