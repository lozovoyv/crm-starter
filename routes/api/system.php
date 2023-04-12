<?php
declare(strict_types=1);

use App\Http\Controllers\API\System\Admins\AdminListController;
use App\Http\Controllers\API\System\Admins\AdminPositionCRUDController;
use App\Http\Controllers\API\System\Admins\AdminPositionHistoryController;
use App\Http\Controllers\API\System\Admins\AdminPositionOperationsController;
use App\Http\Controllers\API\System\Admins\AdminHistoryController;
use App\Http\Controllers\API\System\Admins\AdminPositionViewController;
use App\Http\Controllers\API\System\History\HistoryController;
use App\Http\Controllers\API\System\Permissions\PermissionGroupCRUDController;
use App\Http\Controllers\API\System\Permissions\PermissionGroupsListController;
use App\Http\Controllers\API\System\Permissions\PermissionsHistoryController;
use App\Http\Controllers\API\System\Permissions\PermissionsListController;
use App\Http\Controllers\API\System\Staff\StaffHistoryController;
use App\Http\Controllers\API\System\Staff\StaffListController;
use App\Http\Controllers\API\System\Staff\StaffPositionCRUDController;
use App\Http\Controllers\API\System\Staff\StaffPositionHistoryController;
use App\Http\Controllers\API\System\Staff\StaffPositionOperationsController;
use App\Http\Controllers\API\System\Staff\StaffPositionViewController;
use App\Http\Controllers\API\System\Users\UserCRUDController;
use App\Http\Controllers\API\System\Users\UserHistoryController;
use App\Http\Controllers\API\System\Users\UsersHistoryController;
use App\Http\Controllers\API\System\Users\UsersListController;
use App\Http\Controllers\API\System\Users\UserViewController;
use Illuminate\Support\Facades\Route;

Route::prefix('/api/system/admins')->middleware(['position:admin'])->group(function () {
    Route::get('/', [AdminListController::class, 'list']);
    Route::get('/history', [AdminHistoryController::class, 'list']);
    Route::get('/history/{historyID}/comments', [AdminHistoryController::class, 'comments']);
    Route::get('/history/{historyID}/changes', [AdminHistoryController::class, 'changes']);
    Route::get('/{positionID}', [AdminPositionViewController::class, 'view']);
    Route::get('/position/{positionID?}', [AdminPositionCRUDController::class, 'get']);
    Route::put('/position/{positionID?}', [AdminPositionCRUDController::class, 'save']);
    Route::patch('/position/{positionID}', [AdminPositionCRUDController::class, 'change']);
    Route::delete('/position/{positionID}', [AdminPositionCRUDController::class, 'remove']);
    Route::get('/position/{positionID}/history', [AdminPositionHistoryController::class, 'list']);
    Route::get('/position/{positionID}/history/{historyID}/comments', [AdminPositionHistoryController::class, 'comments']);
    Route::get('/position/{positionID}/history/{historyID}/changes', [AdminPositionHistoryController::class, 'changes']);
    Route::get('/position/{positionID}/operations', [AdminPositionOperationsController::class, 'list']);
    Route::get('/position/{positionID}/operations/{historyID}/comments', [AdminPositionOperationsController::class, 'comments']);
    Route::get('/position/{positionID}/operations/{historyID}/changes', [AdminPositionOperationsController::class, 'changes']);
});

Route::prefix('/api/system/staff')->middleware(['position:admin,staff', 'permission:system.staff,system.staff.change'])->group(function () {
    Route::get('/', [StaffListController::class, 'list']);
    Route::get('/history', [StaffHistoryController::class, 'list']);
    Route::get('/history/{historyID}/comments', [StaffHistoryController::class, 'comments']);
    Route::get('/history/{historyID}/changes', [StaffHistoryController::class, 'changes']);
    Route::get('/{positionID}', [StaffPositionViewController::class, 'view']);
    Route::get('/position/{positionID?}', [StaffPositionCRUDController::class, 'get'])->middleware('permission:system.staff.change');
    Route::put('/position/{positionID?}', [StaffPositionCRUDController::class, 'save'])->middleware('permission:system.staff.change');
    Route::patch('/position/{positionID}', [StaffPositionCRUDController::class, 'change'])->middleware('permission:system.users.change');
    Route::delete('/position/{positionID}', [StaffPositionCRUDController::class, 'remove'])->middleware('permission:system.users.change');
    Route::get('/position/{positionID}/history', [StaffPositionHistoryController::class, 'list']);
    Route::get('/position/{positionID}/history/{historyID}/comments', [StaffPositionHistoryController::class, 'comments']);
    Route::get('/position/{positionID}/history/{historyID}/changes', [StaffPositionHistoryController::class, 'changes']);
    Route::get('/position/{positionID}/operations', [StaffPositionOperationsController::class, 'list']);
    Route::get('/position/{positionID}/operations/{historyID}/comments', [StaffPositionOperationsController::class, 'comments']);
    Route::get('/position/{positionID}/operations/{historyID}/changes', [StaffPositionOperationsController::class, 'changes']);
});

Route::prefix('/api/system/users')->middleware(['position:admin,staff', 'permission:system.users,system.users.change'])->group(function () {
    Route::get('/', [UsersListController::class, 'list']);
    Route::get('/history', [UsersHistoryController::class, 'list']);
    Route::get('/history/{historyID}/comments', [UsersHistoryController::class, 'comments']);
    Route::get('/history/{historyID}/changes', [UsersHistoryController::class, 'changes']);
    Route::get('/{userID}', [UserViewController::class, 'view']);
    Route::get('/user/{userID?}', [UserCRUDController::class, 'get'])->middleware('permission:system.users.change');
    Route::put('/user/{userID?}', [UserCRUDController::class, 'save'])->middleware('permission:system.users.change');
    Route::patch('/user/{userID}', [UserCRUDController::class, 'change'])->middleware('permission:system.users.change');
    Route::patch('/user/{userID}/password', [UserCRUDController::class, 'password'])->middleware('permission:system.users.change');
    Route::delete('/user/{userID}', [UserCRUDController::class, 'remove'])->middleware('permission:system.users.change');
    Route::get('/user/{userID}/history', [UserHistoryController::class, 'list']);
    Route::get('/user/{userID}/history/{historyID}/comments', [UserHistoryController::class, 'comments']);
    Route::get('/user/{userID}/history/{historyID}/changes', [UserHistoryController::class, 'changes']);
});

Route::prefix('/api/system/permissions')->middleware(['position:admin,staff', 'permission:system.permissions'])->group(function () {
    Route::get('/', [PermissionsListController::class, 'list']);
    Route::get('/groups', [PermissionGroupsListController::class, 'list']);
    Route::get('/group/{groupID?}', [PermissionGroupCRUDController::class, 'get']);
    Route::put('/group/{groupID?}', [PermissionGroupCRUDController::class, 'save']);
    Route::delete('/group/{groupID}', [PermissionGroupCRUDController::class, 'remove']);
    Route::patch('/group/{groupID}', [PermissionGroupCRUDController::class, 'change']);
    Route::get('/history', [PermissionsHistoryController::class, 'list']);
    Route::get('/history/{historyID}/comments', [PermissionsHistoryController::class, 'comments']);
    Route::get('/history/{historyID}/changes', [PermissionsHistoryController::class, 'changes']);
});

Route::prefix('/api/system/history')->middleware(['position:admin,staff', 'permission:system.history'])->group(function () {
    Route::get('/', [HistoryController::class, 'list']);
    Route::get('/{historyID}/comments', [HistoryController::class, 'comments']);
    Route::get('/{historyID}/changes', [HistoryController::class, 'changes']);
});
