<?php

use App\Http\Controllers\API\Settings\Roles\PermissionsListController;
use App\Http\Controllers\API\Settings\Roles\RolesController;
use App\Http\Controllers\API\Settings\Roles\RolesEditController;
use App\Http\Controllers\API\Settings\Roles\RolesHistoryController;
use App\Http\Controllers\API\Settings\Roles\RolesListController;
use Illuminate\Support\Facades\Route;

Route::post('/api/settings/roles', [RolesListController::class, 'list']);
Route::post('/api/settings/roles/get', [RolesEditController::class, 'get']);
Route::post('/api/settings/roles/update', [RolesEditController::class, 'update']);
Route::post('/api/settings/roles/activate', [RolesController::class, 'activate']);
Route::post('/api/settings/roles/deactivate', [RolesController::class, 'deactivate']);
Route::post('/api/settings/roles/remove', [RolesController::class, 'remove']);
Route::post('/api/settings/roles/history', [RolesHistoryController::class, 'list']);
Route::post('/api/settings/roles/history/comments', [RolesHistoryController::class, 'comments']);
Route::post('/api/settings/roles/history/changes', [RolesHistoryController::class, 'changes']);
Route::post('/api/settings/roles/permissions', [PermissionsListController::class, 'list']);
