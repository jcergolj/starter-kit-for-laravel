<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ThemeController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'))->name('home');

Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('settings', [SettingsController::class, 'show'])
        ->name('settings');

    Route::prefix('settings')->as('settings.')->group(function () {
        Route::singleton('profile', ProfileController::class)->only(['edit', 'update']);
        Route::get('profile/delete', [ProfileController::class, 'delete'])->name('profile.delete');
        Route::post('profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::singleton('password', PasswordController::class)->only(['edit', 'update']);
    });

    Route::singleton('theme', ThemeController::class)->only(['update']);
    
    // RESTful resource routes
    Route::resource('clients', App\Http\Controllers\ClientController::class);
    Route::resource('projects', App\Http\Controllers\ProjectController::class);
    
    Route::prefix('api')->as('api.')->group(function () {
        Route::post('timer/start', [App\Http\Controllers\TimeTrackingController::class, 'start'])->name('timer.start');
        Route::post('timer/stop', [App\Http\Controllers\TimeTrackingController::class, 'stop'])->name('timer.stop');
        Route::get('timer/current', [App\Http\Controllers\TimeTrackingController::class, 'current'])->name('timer.current');
        Route::get('entries/recent', [App\Http\Controllers\TimeTrackingController::class, 'recent'])->name('entries.recent');
        Route::post('entries', [App\Http\Controllers\TimeTrackingController::class, 'store'])->name('entries.store');
    });
});

Route::get('configurations/android_v1', fn () => new \Illuminate\Http\JsonResponse([
    'patterns' => [
        [
            'patterns' => ['.*'],
            'properties' => [
                'uri' => 'hotwire://fragment/web',
                'pull_to_refresh_enabled' => true,
            ],
        ],
        [
            'patterns' => ['/create/?$', '/edit/?$', '/delete/?$', '/login/?$'],
            'properties' => [
                'context' => 'modal',
                'pull_to_refresh_enabled' => false,
            ],
        ],
    ],
]));

require __DIR__.'/auth.php';
