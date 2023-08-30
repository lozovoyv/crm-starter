<?php
declare(strict_types=1);

use App\Http\Controllers\API\System\Admins\AdminHistoryController;
use App\Http\Controllers\API\System\Admins\AdminListController;
use App\Http\Controllers\API\System\History\HistoryController;
use App\Http\Controllers\API\System\Permissions\PermissionGroupEditController;
use App\Http\Controllers\API\System\Permissions\PermissionGroupHistoryController;
use App\Http\Controllers\API\System\Permissions\PermissionGroupListController;
use App\Http\Controllers\API\System\Permissions\PermissionListController;
use App\Http\Controllers\API\System\Staff\StaffHistoryController;
use App\Http\Controllers\API\System\Staff\StaffListController;
use App\Http\Controllers\API\System\Users\UserEditController;
use App\Http\Controllers\API\System\Users\UserHistoryController;
use App\Http\Controllers\API\System\Users\UserListController;
use App\Http\Controllers\API\System\Users\UserViewController;
use App\Models\Permissions\Permission;
use App\Models\Positions\PositionType;
use Illuminate\Support\Facades\Route;

Route::prefix('/api/system/admins')->middleware(['auth:sanctum', PositionType::middleware(PositionType::admin)])->group(function () {
    Route::get('/', [AdminListController::class, 'list']);
    Route::get('/history', [AdminHistoryController::class, 'list']);
    Route::get('/history/{historyID}/comments', [AdminHistoryController::class, 'comments']);
    Route::get('/history/{historyID}/changes', [AdminHistoryController::class, 'changes']);
    // todo fix Route::get('/{positionID}', [AdminPositionViewController::class, 'view']);
    // todo fix Route::get('/position/{positionID?}', [AdminPositionCRUDController::class, 'get']);
    // todo fix Route::put('/position/{positionID?}', [AdminPositionCRUDController::class, 'save']);
    // todo fix Route::patch('/position/{positionID}/status', [AdminPositionCRUDController::class, 'status']);
    // todo fix Route::delete('/position/{positionID}', [AdminPositionCRUDController::class, 'remove']);
    Route::get('/position/{positionID}/history', [AdminHistoryController::class, 'listForAdmin']);
    Route::get('/position/{positionID}/history/{historyID}/comments', [AdminHistoryController::class, 'commentsForAdmin']);
    Route::get('/position/{positionID}/history/{historyID}/changes', [AdminHistoryController::class, 'changesForAdmin']);
    Route::get('/position/{positionID}/operations', [AdminHistoryController::class, 'listByAdmin']);
    Route::get('/position/{positionID}/operations/{historyID}/comments', [AdminHistoryController::class, 'commentsByAdmin']);
    Route::get('/position/{positionID}/operations/{historyID}/changes', [AdminHistoryController::class, 'changesByAdmin']);
});

Route::prefix('/api/system/staff')->middleware(['auth:sanctum', PositionType::middleware(PositionType::admin, PositionType::staff), Permission::middleware(Permission::system__staff,Permission::system__staff_change)])->group(function () {
    Route::get('/', [StaffListController::class, 'list']);
    Route::get('/history', [StaffHistoryController::class, 'list']);
    Route::get('/history/{historyID}/comments', [StaffHistoryController::class, 'comments']);
    Route::get('/history/{historyID}/changes', [StaffHistoryController::class, 'changes']);
    // todo fix Route::get('/{positionID}', [StaffPositionViewController::class, 'view']);
    // todo fix Route::get('/position/{positionID?}', [StaffPositionCRUDController::class, 'get'])->middleware('permission:system__staff_change');
    // todo fix Route::put('/position/{positionID?}', [StaffPositionCRUDController::class, 'save'])->middleware('permission:system__staff_change');
    // todo fix Route::patch('/position/{positionID}/status', [StaffPositionCRUDController::class, 'status'])->middleware('permission:system__users_change');
    // todo fix Route::delete('/position/{positionID}', [StaffPositionCRUDController::class, 'remove'])->middleware('permission:system__users_change');
    Route::get('/position/{positionID}/history', [StaffHistoryController::class, 'listForStaff']);
    Route::get('/position/{positionID}/history/{historyID}/comments', [StaffHistoryController::class, 'commentsForStaff']);
    Route::get('/position/{positionID}/history/{historyID}/changes', [StaffHistoryController::class, 'changesForStaff']);
    Route::get('/position/{positionID}/operations', [StaffHistoryController::class, 'listByStaff']);
    Route::get('/position/{positionID}/operations/{historyID}/comments', [StaffHistoryController::class, 'commentsByStaff']);
    Route::get('/position/{positionID}/operations/{historyID}/changes', [StaffHistoryController::class, 'changesByStaff']);
});

Route::prefix('/api/system/users')->middleware(['auth:sanctum', PositionType::middleware(PositionType::admin, PositionType::staff), Permission::middleware(Permission::system__users, Permission::system__users_change)])->group(function () {
    Route::get('/', [UserListController::class, 'list']);
    Route::get('/history', [UserHistoryController::class, 'list']);
    Route::get('/history/{historyID}/comments', [UserHistoryController::class, 'comments']);
    Route::get('/history/{historyID}/changes', [UserHistoryController::class, 'changes']);
    Route::get('/user/{userID?}', [UserEditController::class, 'get'])->middleware(Permission::middleware(Permission::system__users_change));
    Route::put('/user/{userID?}', [UserEditController::class, 'save'])->middleware(Permission::middleware(Permission::system__users_change));
    Route::patch('/user/{userID}/status', [UserEditController::class, 'status'])->middleware(Permission::middleware(Permission::system__users_change));
    Route::patch('/user/{userID}/password', [UserEditController::class, 'password'])->middleware(Permission::middleware(Permission::system__users_change));
    Route::patch('/user/{userID}/email', [UserEditController::class, 'email'])->middleware(Permission::middleware(Permission::system__users_change));
    Route::delete('/user/{userID}', [UserEditController::class, 'remove'])->middleware(Permission::middleware(Permission::system__users_change));
    Route::get('/user/{userID}/history', [UserHistoryController::class, 'listForUser']);
    Route::get('/user/{userID}/history/{historyID}/comments', [UserHistoryController::class, 'commentsForUser']);
    Route::get('/user/{userID}/history/{historyID}/changes', [UserHistoryController::class, 'changesForUser']);
    Route::get('/{userID}', [UserViewController::class, 'view']);
});

Route::prefix('/api/system/permissions')->middleware(['auth:sanctum', PositionType::middleware(PositionType::admin, PositionType::staff), Permission::middleware(Permission::system__permissions)])->group(function () {
    Route::get('/', [PermissionListController::class, 'list']);
    Route::get('/groups', [PermissionGroupListController::class, 'list']);
    Route::get('/group/{groupID?}', [PermissionGroupEditController::class, 'get']);
    Route::put('/group/{groupID?}', [PermissionGroupEditController::class, 'save']);
    Route::delete('/group/{groupID}', [PermissionGroupEditController::class, 'remove']);
    Route::patch('/group/{groupID}/status', [PermissionGroupEditController::class, 'status']);
    Route::get('/history', [PermissionGroupHistoryController::class, 'list']);
    Route::get('/history/{historyID}/comments', [PermissionGroupHistoryController::class, 'comments']);
    Route::get('/history/{historyID}/changes', [PermissionGroupHistoryController::class, 'changes']);
});

Route::prefix('/api/system/history')->middleware(['auth:sanctum', PositionType::middleware(PositionType::admin, PositionType::staff), Permission::middleware(Permission::system__history)])->group(function () {
    Route::get('/', [HistoryController::class, 'list']);
    Route::get('/{historyID}/comments', [HistoryController::class, 'comments']);
    Route::get('/{historyID}/changes', [HistoryController::class, 'changes']);
});
