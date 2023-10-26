<?php
declare(strict_types=1);

use App\Http\Controllers\API\System\Admins\AdminHistoryController;
use App\Http\Controllers\API\System\Admins\AdminListController;
use App\Http\Controllers\API\System\History\HistoryController;
use App\Http\Controllers\API\System\Permissions\PermissionGroupEditController;
use App\Http\Controllers\API\System\Permissions\PermissionGroupHistoryController;
use App\Http\Controllers\API\System\Permissions\PermissionGroupListController;
use App\Http\Controllers\API\System\Permissions\PermissionGroupRemoveController;
use App\Http\Controllers\API\System\Permissions\PermissionGroupStatusController;
use App\Http\Controllers\API\System\Permissions\PermissionListController;
use App\Http\Controllers\API\System\Staff\StaffHistoryController;
use App\Http\Controllers\API\System\Staff\StaffListController;
use App\Http\Controllers\API\System\Users\UserEditController;
use App\Http\Controllers\API\System\Users\UserEmailController;
use App\Http\Controllers\API\System\Users\UserHistoryController;
use App\Http\Controllers\API\System\Users\UserListController;
use App\Http\Controllers\API\System\Users\UserPasswordController;
use App\Http\Controllers\API\System\Users\UserRemoveController;
use App\Http\Controllers\API\System\Users\UserStatusController;
use App\Http\Controllers\API\System\Users\UserViewController;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use Illuminate\Support\Facades\Route;

Route::get('/api/system/admins/history', [AdminHistoryController::class, 'list']);
Route::get('/api/system/admins/history/{historyID}/comments', [AdminHistoryController::class, 'comments']);
Route::get('/api/system/admins/history/{historyID}/change', [AdminHistoryController::class, 'change']);
Route::get('/api/system/admins/position/{positionID}/history', [AdminHistoryController::class, 'listForAdmin']);
Route::get('/api/system/admins/position/{positionID}/history/{historyID}/comments', [AdminHistoryController::class, 'commentsForAdmin']);
Route::get('/api/system/admins/position/{positionID}/history/{historyID}/change', [AdminHistoryController::class, 'changeForAdmin']);
Route::get('/api/system/admins/position/{positionID}/operations', [AdminHistoryController::class, 'listByAdmin']);
Route::get('/api/system/admins/position/{positionID}/operations/{historyID}/comments', [AdminHistoryController::class, 'commentsByAdmin']);
Route::get('/api/system/admins/position/{positionID}/operations/{historyID}/change', [AdminHistoryController::class, 'changeByAdmin']);

Route::prefix('/api/system/admins')->middleware(['auth:sanctum', PositionType::middleware(PositionType::admin)])->group(function () {
    Route::get('/', [AdminListController::class, 'list']);
    // todo fix Route::get('/{positionID}', [AdminPositionViewController::class, 'view']);
    // todo fix Route::get('/position/{positionID?}', [AdminPositionCRUDController::class, 'get']);
    // todo fix Route::put('/position/{positionID?}', [AdminPositionCRUDController::class, 'save']);
    // todo fix Route::patch('/position/{positionID}/status', [AdminPositionCRUDController::class, 'status']);
    // todo fix Route::delete('/position/{positionID}', [AdminPositionCRUDController::class, 'remove']);
});

Route::get('/api/system/staff/history', [StaffHistoryController::class, 'list']);
Route::get('/api/system/staff/history/{historyID}/comments', [StaffHistoryController::class, 'comments']);
Route::get('/api/system/staff/history/{historyID}/change', [StaffHistoryController::class, 'change']);
Route::get('/api/system/staff/position/{positionID}/history', [StaffHistoryController::class, 'listForStaff']);
Route::get('/api/system/staff/position/{positionID}/history/{historyID}/comments', [StaffHistoryController::class, 'commentsForStaff']);
Route::get('/api/system/staff/position/{positionID}/history/{historyID}/change', [StaffHistoryController::class, 'changeForStaff']);
Route::get('/api/system/staff/position/{positionID}/operations', [StaffHistoryController::class, 'listByStaff']);
Route::get('/api/system/staff/position/{positionID}/operations/{historyID}/comments', [StaffHistoryController::class, 'commentsByStaff']);
Route::get('/api/system/staff/position/{positionID}/operations/{historyID}/change', [StaffHistoryController::class, 'changeByStaff']);

Route::prefix('/api/system/staff')->middleware(
    ['auth:sanctum', PositionType::middleware(PositionType::admin, PositionType::staff), Permission::middleware(Permission::system__staff, Permission::system__staff_change)]
)->group(function () {
    Route::get('/', [StaffListController::class, 'list']);
    // todo fix Route::get('/{positionID}', [StaffPositionViewController::class, 'view']);
    // todo fix Route::get('/position/{positionID?}', [StaffPositionCRUDController::class, 'get'])->middleware('permission:system__staff_change');
    // todo fix Route::put('/position/{positionID?}', [StaffPositionCRUDController::class, 'save'])->middleware('permission:system__staff_change');
    // todo fix Route::patch('/position/{positionID}/status', [StaffPositionCRUDController::class, 'status'])->middleware('permission:system__users_change');
    // todo fix Route::delete('/position/{positionID}', [StaffPositionCRUDController::class, 'remove'])->middleware('permission:system__users_change');
});

// Users
Route::get('/api/system/users', UserListController::class);
Route::get('/api/system/users/history', [UserHistoryController::class, 'list']);
Route::get('/api/system/users/history/{historyID}/comments', [UserHistoryController::class, 'comments']);
Route::get('/api/system/users/history/{historyID}/change', [UserHistoryController::class, 'change']);
Route::get('/api/system/users/user/{userID?}', [UserEditController::class, 'get']);
Route::put('/api/system/users/user/{userID?}', [UserEditController::class, 'put']);
Route::patch('/api/system/users/user/{userID}/status', UserStatusController::class);
Route::patch('/api/system/users/user/{userID}/password', UserPasswordController::class);
Route::patch('/api/system/users/user/{userID}/email', UserEmailController::class);
Route::delete('/api/system/users/user/{userID}', UserRemoveController::class);
Route::get('/api/system/users/user/{userID}/history', [UserHistoryController::class, 'listForUser']);
Route::get('/api/system/users/user/{userID}/history/{historyID}/comments', [UserHistoryController::class, 'commentsForUser']);
Route::get('/api/system/users/user/{userID}/history/{historyID}/change', [UserHistoryController::class, 'changeForUser']);
Route::get('/api/system/users/{userID}', UserViewController::class);

// Permissions
Route::get('/api/system/permissions', PermissionListController::class);
Route::get('/api/system/permissions/groups', PermissionGroupListController::class);
Route::get('/api/system/permissions/group/{groupID?}', [PermissionGroupEditController::class, 'get']);
Route::put('/api/system/permissions/group/{groupID?}', [PermissionGroupEditController::class, 'put']);
Route::patch('/api/system/permissions/group/{groupID}/status', PermissionGroupStatusController::class);
Route::delete('/api/system/permissions/group/{groupID}', PermissionGroupRemoveController::class);
Route::get('/api/system/permissions/history', [PermissionGroupHistoryController::class, 'list']);
Route::get('/api/system/permissions/history/{historyID}/comments', [PermissionGroupHistoryController::class, 'comments']);
Route::get('/api/system/permissions/history/{historyID}/change', [PermissionGroupHistoryController::class, 'change']);

// Common history
Route::get('/api/system/history', [HistoryController::class, 'list']);
Route::get('/api/system/history/{historyID}/comments', [HistoryController::class, 'comments']);
Route::get('/api/system/history/{historyID}/change', [HistoryController::class, 'change']);
