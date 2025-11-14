<?php

use App\Http\Controllers\Auth\AuthenticatedTokenController;
use App\Http\Controllers\PriceHistoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rota de autenticação
Route::post('/login', [AuthenticatedTokenController::class, 'store']);

// Rotas do usuários logados
Route::middleware(['auth:sanctum'])->group(function() {
    Route::post('/logout', [AuthenticatedTokenController::class, 'destroy'])->middleware('auth:sanctum');

    // Principais rotas
    Route::apiResource('/products', ProductController::class);
    Route::apiResource('/subscriptions', SubscriptionController::class);
    Route::apiResource('/price-histories', PriceHistoryController::class);

    // Rota do administrador
    Route::middleware(['can:admin'])->group(function () {
        Route::apiResource('/users', UserController::class);
    });

});


