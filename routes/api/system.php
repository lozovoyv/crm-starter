<?php

use App\Http\Controllers\API\System\HistoryController;
use App\Http\Controllers\API\System\Roles\PermissionsListController;
use App\Http\Controllers\API\System\Roles\RolesController;
use App\Http\Controllers\API\System\Roles\RolesEditController;
use App\Http\Controllers\API\System\Roles\RolesHistoryController;
use App\Http\Controllers\API\System\Roles\RolesListController;
use Illuminate\Support\Facades\Route;

Route::post('/api/system/roles', [RolesListController::class, 'list'])->middleware(['position:staff', 'permission:system.roles']);
Route::post('/api/system/roles/get', [RolesEditController::class, 'get'])->middleware(['position:staff', 'permission:system.roles']);
Route::post('/api/system/roles/update', [RolesEditController::class, 'update'])->middleware(['position:staff', 'permission:system.roles']);
Route::post('/api/system/roles/activate', [RolesController::class, 'activate'])->middleware(['position:staff', 'permission:system.roles']);
Route::post('/api/system/roles/deactivate', [RolesController::class, 'deactivate'])->middleware(['position:staff', 'permission:system.roles']);
Route::post('/api/system/roles/remove', [RolesController::class, 'remove'])->middleware(['position:staff', 'permission:system.roles']);
Route::post('/api/system/roles/history', [RolesHistoryController::class, 'list'])->middleware(['position:staff', 'permission:system.roles']);
Route::post('/api/system/roles/history/comments', [RolesHistoryController::class, 'comments'])->middleware(['position:staff', 'permission:system.roles']);
Route::post('/api/system/roles/history/changes', [RolesHistoryController::class, 'changes'])->middleware(['position:staff', 'permission:system.roles']);
Route::post('/api/system/roles/permissions', [PermissionsListController::class, 'list'])->middleware(['position:staff', 'permission:system.roles']);

Route::post('/api/system/history', [HistoryController::class, 'list'])->middleware(['position:staff', 'permission:system.history']);
Route::post('/api/system/history/comments', [HistoryController::class, 'comments'])->middleware(['position:staff', 'permission:system.history']);
Route::post('/api/system/history/changes', [HistoryController::class, 'changes'])->middleware(['position:staff', 'permission:system.history']);
