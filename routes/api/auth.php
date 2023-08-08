<?php
declare(strict_types=1);

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\EmailConfirmController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\PasswordController;
use App\Http\Controllers\API\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::prefix('/api/auth')->middleware(['api'])->group(function () {
    Route::get('/config', [AuthController::class, 'config']);

    Route::get('/current', [AuthController::class, 'current']);
    Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('guest');
    Route::post('/password/forget', [PasswordController::class, 'forget'])->middleware('guest');
    Route::post('/password/reset', [PasswordController::class, 'reset'])->middleware('guest');
    Route::post('/confirm/email', [EmailConfirmController::class, 'confirm']);
});
