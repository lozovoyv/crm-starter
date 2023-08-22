<?php
declare(strict_types=1);

use App\Http\Controllers\API\Dictionary\DictionaryController;
use Illuminate\Support\Facades\Route;

/**
 * Permissions and position types able to view or modify dictionaries
 * are checked inside Dictionary instance.
 */
Route::get('/api/dictionary/{alias}', [DictionaryController::class, 'view'])->middleware(['auth:sanctum']);

Route::prefix('/api/dictionaries')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [DictionaryController::class, 'index'])->middleware(['position:admin,staff', 'permission:system.dictionaries']);
    Route::get('/{alias}', [DictionaryController::class, 'list'])->middleware(['position:admin,staff', 'permission:system.dictionaries']);
    Route::patch('/{alias}', [DictionaryController::class, 'sync'])->middleware(['position:admin,staff', 'permission:system.dictionaries']);
    Route::patch('/{alias}/{ID}', [DictionaryController::class, 'toggle'])->middleware(['position:admin,staff', 'permission:system.dictionaries']);
    Route::get('/{alias}/{ID}', [DictionaryController::class, 'get']);
    Route::put('/{alias}/{ID}', [DictionaryController::class, 'update']);
    Route::delete('/{alias}/{ID}', [DictionaryController::class, 'delete']);
});
