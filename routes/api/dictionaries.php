<?php
declare(strict_types=1);

use App\Http\Controllers\API\Dictionary\DictionaryController;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use Illuminate\Support\Facades\Route;

/**
 * Permissions and position types able to view or modify dictionaries
 * are checked inside Dictionary instance.
 */
Route::get('/api/dictionary/{alias}', [DictionaryController::class, 'view'])->middleware(['auth:sanctum']);

Route::prefix('/api/dictionaries')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [DictionaryController::class, 'index'])->middleware([PositionType::middleware(PositionType::admin, PositionType::staff), Permission::middleware(Permission::system__dictionaries)]);
    Route::get('/{alias}', [DictionaryController::class, 'list'])->middleware([PositionType::middleware(PositionType::admin, PositionType::staff), Permission::middleware(Permission::system__dictionaries)]);
    Route::post('/{alias}/order', [DictionaryController::class, 'sync'])->middleware([PositionType::middleware(PositionType::admin, PositionType::staff), Permission::middleware(Permission::system__dictionaries)]);
    Route::get('/{alias}/item/{ID?}', [DictionaryController::class, 'get']);
    Route::post('/{alias}/item/{ID?}', [DictionaryController::class, 'update']);
    Route::patch('/{alias}/item/{ID}', [DictionaryController::class, 'toggle']);
    Route::delete('/{alias}/item/{ID}', [DictionaryController::class, 'delete']);
});
