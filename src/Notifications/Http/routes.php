<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Notifications\Http\Controllers\ExecuteNotificationController;
use Src\Notifications\Http\Controllers\GetNotificationsByContextController;
use Src\Notifications\Http\Controllers\GetUnreadNotificationsController;
use Src\Notifications\Http\Controllers\ListNotificationsController;
use Src\Notifications\Http\Controllers\ListNotificationsLogsController;
use Src\Notifications\Http\Controllers\ListNotificationsLogsPageController;
use Src\Notifications\Http\Controllers\ShowNotificationController;

Route::middleware(['web', 'auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::post('/by-context', GetNotificationsByContextController::class)->name('by-context');
    Route::post('/unread', GetUnreadNotificationsController::class)->name('unread');

    Route::get('/', ListNotificationsLogsPageController::class)->name('list-page');
    Route::post('/', ListNotificationsController::class)->name('list');

    Route::post('/{id}/show', ShowNotificationController::class)->name('show');
    Route::post('/{id}/complete', ExecuteNotificationController::class)->name('complete');

    Route::get('/logs', ListNotificationsLogsPageController::class)->name('logs.list-page');
    Route::post('/logs', ListNotificationsLogsController::class)->name('logs.list');
});
