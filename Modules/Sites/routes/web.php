<?php

use Illuminate\Support\Facades\Route;
use Modules\Sites\Http\Controllers\SitesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('sites', SitesController::class)->names('sites');
});
