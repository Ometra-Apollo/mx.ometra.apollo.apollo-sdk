<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Ometra\Apollo\Sdk\Http\Controllers\ProteusDirectoryController;
use Ometra\Apollo\Sdk\Http\Controllers\SuiteApplicationController;

Route::prefix('_apollo')
    ->middleware((array) config('apollo.frontend.middleware', ['web', 'caronte.session']))
    ->group(function (): void {
        Route::get('/proteus/directories', [ProteusDirectoryController::class, 'index']);
        Route::post('/proteus/directories', [ProteusDirectoryController::class, 'store']);
        Route::get('/suite/applications/user', [SuiteApplicationController::class, 'userApplications']);
    });
