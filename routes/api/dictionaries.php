<?php
declare(strict_types=1);

use App\Http\Controllers\API\Dictionary\DictionaryController;
use Illuminate\Support\Facades\Route;

/**
 * Permissions and position types able to view or modify dictionaries
 * are checked inside DictionaryController based on dictionary configs:
 * config/dictionaries.php
 */
Route::get('/api/dictionary/{alias}', [DictionaryController::class, 'view'])->middleware(['auth:sanctum']);

Route::prefix('/api/dictionaries')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [DictionaryController::class, 'index']);
    Route::get('/{alias}', [DictionaryController::class, 'list']);
    Route::patch('/{alias}', [DictionaryController::class, 'sync']);
    Route::get('/{alias}/{ID}', [DictionaryController::class, 'get']);
    Route::put('/{alias}/{ID}', [DictionaryController::class, 'update']);
    Route::patch('/{alias}/{ID}', [DictionaryController::class, 'toggle']);
    Route::delete('/{alias}/{ID}', [DictionaryController::class, 'delete']);
});
