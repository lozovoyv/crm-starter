<?php

use App\Http\Controllers\API\System\HistoryController;
use App\Http\Controllers\API\System\Roles\PermissionsListController;
use App\Http\Controllers\API\System\Roles\RolesController;
use App\Http\Controllers\API\System\Roles\RolesEditController;
use App\Http\Controllers\API\System\Roles\RolesHistoryController;
use App\Http\Controllers\API\System\Roles\RolesListController;
use App\Http\Controllers\API\System\Staff\StaffCreateController;
use App\Http\Controllers\API\System\Staff\StaffAllHistoryController;
use App\Http\Controllers\API\System\Staff\StaffHistoryController;
use App\Http\Controllers\API\System\Staff\StaffListController;
use App\Http\Controllers\API\System\Staff\StaffOperationsController;
use App\Http\Controllers\API\System\Staff\StaffViewController;
use App\Http\Controllers\API\System\Users\UserEditController;
use App\Http\Controllers\API\System\Users\UserHistoryController;
use App\Http\Controllers\API\System\Users\UsersController;
use App\Http\Controllers\API\System\Users\UsersHistoryController;
use App\Http\Controllers\API\System\Users\UsersListController;
use App\Http\Controllers\API\System\Users\UserViewController;
use Illuminate\Support\Facades\Route;

Route::post('/api/system/staff', [StaffListController::class, 'list'])->middleware(['position:staff', 'permission:system.staff,system.staff.change']);
Route::post('/api/system/staff/view', [StaffViewController::class, 'view'])->middleware(['position:staff', 'permission:system.staff,system.staff.change']);
Route::post('/api/system/staff/get', [StaffCreateController::class, 'get'])->middleware(['position:staff', 'permission:system.staff.change']);
Route::post('/api/system/staff/create', [StaffCreateController::class, 'create'])->middleware(['position:staff', 'permission:system.staff.change']);
Route::post('/api/system/staff/history', [StaffAllHistoryController::class, 'list'])->middleware(['position:staff', 'permission:system.staff,system.staff.change']);
Route::post('/api/system/staff/history/comments', [StaffAllHistoryController::class, 'comments'])->middleware(['position:staff', 'permission:system.staff,system.staff.change']);
Route::post('/api/system/staff/history/changes', [StaffAllHistoryController::class, 'changes'])->middleware(['position:staff', 'permission:system.staff,system.staff.change']);
Route::post('/api/system/staff/{id}/history', [StaffHistoryController::class, 'list'])->middleware(['position:staff', 'permission:system.staff,system.staff.change']);
Route::post('/api/system/staff/{id}/history/comments', [StaffHistoryController::class, 'comments'])->middleware(['position:staff', 'permission:system.staff,system.staff.change']);
Route::post('/api/system/staff/{id}/history/changes', [StaffHistoryController::class, 'changes'])->middleware(['position:staff', 'permission:system.staff,system.staff.change']);
Route::post('/api/system/staff/{id}/operations', [StaffOperationsController::class, 'list'])->middleware(['position:staff', 'permission:system.staff,system.staff.change']);
Route::post('/api/system/staff/{id}/operations/comments', [StaffOperationsController::class, 'comments'])->middleware(['position:staff', 'permission:system.staff,system.staff.change']);
Route::post('/api/system/staff/{id}/operations/changes', [StaffOperationsController::class, 'changes'])->middleware(['position:staff', 'permission:system.staff,system.staff.change']);

Route::post('/api/system/users', [UsersListController::class, 'list'])->middleware(['position:staff', 'permission:system.users,system.users.change']);
Route::post('/api/system/users/view', [UserViewController::class, 'view'])->middleware(['position:staff', 'permission:system.users,system.users.change']);
Route::post('/api/system/users/get', [UserEditController::class, 'get'])->middleware(['position:staff', 'permission:system.users.change']);
Route::post('/api/system/users/update', [UserEditController::class, 'update'])->middleware(['position:staff', 'permission:system.users.change']);
Route::post('/api/system/users/activate', [UsersController::class, 'activate'])->middleware(['position:staff', 'permission:system.users.change']);
Route::post('/api/system/users/deactivate', [UsersController::class, 'deactivate'])->middleware(['position:staff', 'permission:system.users.change']);
Route::post('/api/system/users/remove', [UsersController::class, 'remove'])->middleware(['position:staff', 'permission:system.users.change']);
Route::post('/api/system/users/history', [UsersHistoryController::class, 'list'])->middleware(['position:staff', 'permission:system.users,system.users.change']);
Route::post('/api/system/users/history/comments', [UsersHistoryController::class, 'comments'])->middleware(['position:staff', 'permission:system.users,system.users.change']);
Route::post('/api/system/users/history/changes', [UsersHistoryController::class, 'changes'])->middleware(['position:staff', 'permission:system.users,system.users.change']);
Route::post('/api/system/users/{id}/history', [UserHistoryController::class, 'list'])->middleware(['position:staff', 'permission:system.users,system.users.change']);
Route::post('/api/system/users/{id}/history/comments', [UserHistoryController::class, 'comments'])->middleware(['position:staff', 'permission:system.users,system.users.change']);
Route::post('/api/system/users/{id}/history/changes', [UserHistoryController::class, 'changes'])->middleware(['position:staff', 'permission:system.users,system.users.change']);

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
