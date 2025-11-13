<?php

use App\Http\Controllers\Auth\AuthenticatedTokenController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthenticatedTokenController::class, 'store']);

Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('/logout', [AuthenticatedTokenController::class, 'destroy'])->middleware('auth:sanctum');

    Route::apiResource('/products', ProductController::class)->except('index');
    Route::apiResource('/subscriptions', SubscriptionController::class)->except('index');

    // Rota do administrador
    Route::middleware(['can:admin'])->group(function () {
        Route::apiResource('/users', UserController::class);
        
        Route::get('subscriptions', [SubscriptionController::class, 'index']);
        Route::get('products', [ProductController::class, 'index']);
    });
});


