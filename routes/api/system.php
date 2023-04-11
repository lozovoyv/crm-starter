<?php
declare(strict_types=1);

use App\Http\Controllers\API\System\HistoryController;
use App\Http\Controllers\API\System\Permissions\PermissionsListController;
use App\Http\Controllers\API\System\Permissions\PermissionGroupCRUDController;
use App\Http\Controllers\API\System\Permissions\PermissionsHistoryController;
use App\Http\Controllers\API\System\Permissions\PermissionGroupsListController;
use App\Http\Controllers\API\System\StaffPositions\StaffCreateController;
use App\Http\Controllers\API\System\StaffPositions\StaffAllHistoryController;
use App\Http\Controllers\API\System\StaffPositions\StaffHistoryController;
use App\Http\Controllers\API\System\StaffPositions\StaffListController;
use App\Http\Controllers\API\System\StaffPositions\StaffOperationsController;
use App\Http\Controllers\API\System\StaffPositions\StaffViewController;
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
Route::post('/api/system/users/password', [UsersController::class, 'password'])->middleware(['position:staff', 'permission:system.users.change']);
Route::post('/api/system/users/remove', [UsersController::class, 'remove'])->middleware(['position:staff', 'permission:system.users.change']);
Route::post('/api/system/users/history', [UsersHistoryController::class, 'list'])->middleware(['position:staff', 'permission:system.users,system.users.change']);
Route::post('/api/system/users/history/comments', [UsersHistoryController::class, 'comments'])->middleware(['position:staff', 'permission:system.users,system.users.change']);
Route::post('/api/system/users/history/changes', [UsersHistoryController::class, 'changes'])->middleware(['position:staff', 'permission:system.users,system.users.change']);
Route::post('/api/system/users/{id}/history', [UserHistoryController::class, 'list'])->middleware(['position:staff', 'permission:system.users,system.users.change']);
Route::post('/api/system/users/{id}/history/comments', [UserHistoryController::class, 'comments'])->middleware(['position:staff', 'permission:system.users,system.users.change']);
Route::post('/api/system/users/{id}/history/changes', [UserHistoryController::class, 'changes'])->middleware(['position:staff', 'permission:system.users,system.users.change']);

Route::get('/api/system/permissions/groups', [PermissionGroupsListController::class, 'list'])->middleware(['position:admin,staff', 'permission:system.permissions']);
Route::get('/api/system/permissions/group/{id?}', [PermissionGroupCRUDController::class, 'get'])->middleware(['position:admin,staff', 'permission:system.permissions']);
Route::put('/api/system/permissions/group/{id?}', [PermissionGroupCRUDController::class, 'update'])->middleware(['position:admin,staff', 'permission:system.permissions']);
Route::delete('/api/system/permissions/group/{id}', [PermissionGroupCRUDController::class, 'remove'])->middleware(['position:admin,staff', 'permission:system.permissions']);
Route::patch('/api/system/permissions/group/{id}', [PermissionGroupCRUDController::class, 'change'])->middleware(['position:admin,staff', 'permission:system.permissions']);
Route::get('/api/system/permissions/permissions', [PermissionsListController::class, 'list'])->middleware(['position:admin,staff', 'permission:system.permissions']);
Route::get('/api/system/permissions/history', [PermissionsHistoryController::class, 'list'])->middleware(['position:admin,staff', 'permission:system.permissions']);
Route::get('/api/system/permissions/history/{id}/comments', [PermissionsHistoryController::class, 'comments'])->middleware(['position:admin,staff', 'permission:system.permissions']);
Route::get('/api/system/permissions/history/{id}/changes', [PermissionsHistoryController::class, 'changes'])->middleware(['position:admin,staff', 'permission:system.permissions']);

Route::post('/api/system/history', [HistoryController::class, 'list'])->middleware(['position:staff', 'permission:system.history']);
Route::post('/api/system/history/comments', [HistoryController::class, 'comments'])->middleware(['position:staff', 'permission:system.history']);
Route::post('/api/system/history/changes', [HistoryController::class, 'changes'])->middleware(['position:staff', 'permission:system.history']);
