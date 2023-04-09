<?php
declare(strict_types=1);

use App\Http\Controllers\Front\FrontendController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::name('frontend')
        ->any('/{query?}', [FrontendController::class, 'index'])
        ->where('query', '[\/\w\.-]*');
});
