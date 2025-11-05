<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UrlController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // URL management routes
    Route::apiResource('urls', UrlController::class)->except(['update']);

    // Admin routes
    Route::middleware(EnsureUserIsAdmin::class)->prefix('admin')->group(function () {
        Route::get('/urls', [AdminController::class, 'index']);
        Route::delete('/urls/{url}', [AdminController::class, 'destroy']);
    });
});

// Public redirect route (outside API to avoid /api prefix)
Route::get('/{code}', [UrlController::class, 'redirect'])->name('url.redirect');
