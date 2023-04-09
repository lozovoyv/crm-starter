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
Route::delete('/api/dictionaries/{alias}', [DictionaryController::class, 'delete']);
Route::get('/api/dictionaries/{alias}/{id}', [DictionaryController::class, 'get']);
Route::post('/api/dictionaries/{alias}/{id}', [DictionaryController::class, 'update']);
Route::patch('/api/dictionaries/{alias}/{id}', [DictionaryController::class, 'toggle']);
