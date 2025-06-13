<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PromoCodeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Auth routes
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::prefix('promo-codes')->middleware('auth:sanctum')->group(function () {
        Route::post('/', [PromoCodeController::class, 'store']);
        Route::post('/redeem', [PromoCodeController::class, 'redeem'])->middleware('throttle:100,1');
    });
});
