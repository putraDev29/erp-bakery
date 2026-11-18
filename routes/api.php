<?php

use App\Http\Controllers\Api\ApiAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [ApiAuthController::class, 'login']);

    Route::middleware('jwt.auth')->group(function () {
        Route::get('/me', [ApiAuthController::class, 'me']);
        Route::post('/logout', [ApiAuthController::class, 'logout']);
    });
});
