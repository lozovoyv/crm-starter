<?php

use App\Http\Controllers\API\Settings\Roles\RolesListController;
use Illuminate\Support\Facades\Route;

Route::post('/api/settings/roles', [RolesListController::class, 'list']);
