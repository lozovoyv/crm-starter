<?php
declare(strict_types=1);

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\EmailConfirmController;
use App\Http\Controllers\API\NotFoundController;
use Illuminate\Support\Facades\Route;

//Route::middleware(['api'])->group(function () {
//    Route::post('/api/current', [AuthController::class, 'current']);
//    Route::post('/api/login', [AuthController::class, 'login'])->middleware('guest');
//    Route::post('/api/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
//    Route::post('/api/confirm/email', [EmailConfirmController::class, 'confirm']);
//});

Route::middleware(['api'])->group(function () {

    $apiRouteFiles = glob(base_path('routes/api/*.php'));

    foreach ($apiRouteFiles as $file) {
        require($file);
    }

});

Route::any('/api/{any}', [NotFoundController::class, 'notFound'])->where('any', '.*');
