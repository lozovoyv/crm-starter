<?php
declare(strict_types=1);

use App\Http\Controllers\API\Dictionary\DictionaryController;
use Illuminate\Support\Facades\Route;

/**
 * Permissions and position types able to view or modify dictionaries
 * are checked inside DictionaryController based on dictionary configs:
 * config/dictionaries.php
 */
Route::get('/api/dictionary/{alias}', [DictionaryController::class, 'view']);

Route::get('/api/dictionaries', [DictionaryController::class, 'index']);
Route::get('/api/dictionaries/{alias}', [DictionaryController::class, 'list']);
Route::patch('/api/dictionaries/{alias}', [DictionaryController::class, 'sync']);
Route::get('/api/dictionaries/{alias}/{ID}', [DictionaryController::class, 'get']);
Route::put('/api/dictionaries/{alias}/{ID}', [DictionaryController::class, 'update']);
Route::patch('/api/dictionaries/{alias}/{ID}', [DictionaryController::class, 'toggle']);
Route::delete('/api/dictionaries/{alias}/{ID}', [DictionaryController::class, 'delete']);
