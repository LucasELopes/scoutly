<?php

use App\Http\Controllers\Auth\AuthenticatedTokenController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthenticatedTokenController::class, 'store']);
Route::post('/logout', [AuthenticatedTokenController::class, 'destroy'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function() {
    Route::apiResource('/subscriptions', SubscriptionController::class);
});

