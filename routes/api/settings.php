<?php

use App\Http\Controllers\API\Settings\Roles\RolesController;
use App\Http\Controllers\API\Settings\Roles\RolesListController;
use Illuminate\Support\Facades\Route;

Route::post('/api/settings/roles', [RolesListController::class, 'list']);
Route::post('/api/settings/roles/activate', [RolesController::class, 'activate']);
Route::post('/api/settings/roles/deactivate', [RolesController::class, 'deactivate']);
Route::post('/api/settings/roles/remove', [RolesController::class, 'remove']);
