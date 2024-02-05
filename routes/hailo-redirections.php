<?php

use Illuminate\Support\Facades\Route;
use Pardalsalcap\HailoRedirections\Livewire\Redirections\RedirectionsApp;

Route::middleware(['web'])
    ->prefix(config('hailo.route'))
    ->name('hailo.')
    ->group(function () {
        Route::middleware([\Pardalsalcap\Hailo\Middleware\HailoAuthMiddleware::class, 'role:admin|super-admin'])->group(function () {
            Route::get('/redirections', RedirectionsApp::class)->name('redirections');
        });
    });
