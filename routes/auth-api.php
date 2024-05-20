<?php
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'api',
])->prefix('auth')->group(function () {

    Route::controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
        Route::post('login', 'login');
    });
});
