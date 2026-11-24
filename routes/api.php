
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentAuthController;

// Public Student Mobile Login
Route::prefix('student')
    ->middleware('throttle:10,1')
    ->group(function () {
        Route::post('/login', [StudentAuthController::class, 'login']);
    });

// Protected Student Mobile APIs
Route::prefix('student')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/profile', [StudentAuthController::class, 'profile']);
        Route::post('/change-password', [StudentAuthController::class, 'changePassword']);
        Route::post('/logout', [StudentAuthController::class, 'logout']);
    });


require __DIR__.'/teacher_api.php';    