<?php
declare(strict_types=1);

use App\Http\Controllers\API\NotFoundController;
use App\Http\Controllers\API\System\EmailConfirmController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    Route::post('/api/current', [AuthController::class, 'current']);
    Route::post('/api/login', [AuthController::class, 'login'])->middleware('guest');
    Route::post('/api/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/api/confirm/email', [EmailConfirmController::class, 'confirm']);
});

Route::middleware(['api', 'auth:sanctum'])->group(function () {

    $apiRouteFiles = glob(base_path('routes/api/*.php'));

    foreach ($apiRouteFiles as $file) {
        require($file);
    }

});

Route::any('/api/{any}', [NotFoundController::class, 'notFound'])->where('any', '.*');
