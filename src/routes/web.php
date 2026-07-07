<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Ometra\Apollo\Sdk\Http\Controllers\ProteusDirectoryController;

Route::prefix((string) config('apollo.frontend.route_prefix', '_apollo'))
    ->middleware((array) config('apollo.frontend.middleware', ['web', 'caronte.session']))
    ->group(function (): void {
        Route::get('/proteus/directories', [ProteusDirectoryController::class, 'index']);
    });
