<?php

use Illuminate\Support\Facades\Route;
use Modules\Sites\Http\Controllers\SitesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sites', SitesController::class)->names('sites');
});
