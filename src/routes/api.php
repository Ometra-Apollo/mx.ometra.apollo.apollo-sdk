<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Ometra\Apollo\Sdk\Http\Controllers\IgnisGroupController;

Route::prefix((string) config('apollo.ignis_groups.route_prefix', 'api/ignis'))
    ->middleware((array) config('apollo.ignis_groups.middleware', ['caronte.application:tenant_required']))
    ->group(function (): void {
        Route::get('/groups', [IgnisGroupController::class, 'index']);
    });
