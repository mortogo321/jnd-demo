<?php

use App\Http\Controllers\Api\UrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public redirect route for shortened URLs
Route::get('/{code}', [UrlController::class, 'redirect'])->name('url.redirect');
