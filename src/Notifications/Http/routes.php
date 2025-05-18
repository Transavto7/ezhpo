<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Notifications\Http\Controllers\MarkNotificationAsCompletedController;
use Src\Notifications\Http\Controllers\CreateNotificationsByContextController;
use Src\Notifications\Http\Controllers\GetUnreadNotificationsController;
use Src\Notifications\Http\Controllers\GetNotificationTableItemsController;
use Src\Notifications\Http\Controllers\GetNotificationLogTableItemsController;
use Src\Notifications\Http\Controllers\ListNotificationsLogsPageController;
use Src\Notifications\Http\Controllers\ListNotificationsPageController;
use Src\Notifications\Http\Controllers\MarkNotificationAsViewedController;
use Src\Notifications\Http\Controllers\Selects\SelectActionsController;
use Src\Notifications\Http\Controllers\Selects\SelectNotificationsController;
use Src\Notifications\Http\Controllers\Selects\SelectRemindersController;
use Src\Notifications\Http\Controllers\Selects\SelectUsersController;
use Src\Notifications\Http\Controllers\MarkNotificationAsReadController;

Route::middleware(['web', 'auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::post('/by-context', CreateNotificationsByContextController::class)->name('by-context');
    Route::post('/unread', GetUnreadNotificationsController::class)->name('unread');

    Route::get('/', ListNotificationsPageController::class)->name('list-page');
    Route::post('/', GetNotificationTableItemsController::class)->name('list');

    Route::post('/{id}/mark-as-viewed', MarkNotificationAsViewedController::class)->name('viewed');
    Route::post('/{id}/mark-as-read', MarkNotificationAsReadController::class)->name('read');
    Route::post('/{id}/mark-as-completed', MarkNotificationAsCompletedController::class)->name('complete');

    Route::get('/actions/select', SelectActionsController::class)->name('actions.select');
    Route::get('/users/select', SelectUsersController::class)->name('users.select');
    Route::get('/reminders/select', SelectRemindersController::class)->name('users.reminders');
    Route::get('/select/items', SelectNotificationsController::class)->name('users.notifications');

    Route::get('/logs', ListNotificationsLogsPageController::class)->name('logs.list-page');
    Route::post('/logs', GetNotificationLogTableItemsController::class)->name('logs.list');
});
