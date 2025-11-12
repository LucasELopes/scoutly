<?php

use App\Http\Controllers\Auth\AuthenticatedTokenController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthenticatedTokenController::class, 'store']);

Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('/logout', [AuthenticatedTokenController::class, 'destroy'])->middleware('auth:sanctum');

    Route::apiResource('/products', ProductController::class);
    Route::apiResource('/subscriptions', SubscriptionController::class);

    // Rota do administrador
    Route::middleware(['can:admin'])->group(function () {
        Route::apiResource('/users', UserController::class);
    });
});


