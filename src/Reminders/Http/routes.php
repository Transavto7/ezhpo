<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Reminders\Http\Controllers\CreateReminderController;
use Src\Reminders\Http\Controllers\CreateReminderPageController;
use Src\Reminders\Http\Controllers\DeleteReminderController;
use Src\Reminders\Http\Controllers\JournalReminderLogsController;
use Src\Reminders\Http\Controllers\JournalReminderLogsPageController;
use Src\Reminders\Http\Controllers\ListRemindersController;
use Src\Reminders\Http\Controllers\ListRemindersPageController;
use Src\Reminders\Http\Controllers\Selects\SelectActionsController;
use Src\Reminders\Http\Controllers\Selects\SelectCitiesController;
use Src\Reminders\Http\Controllers\Selects\SelectCompaniesController;
use Src\Reminders\Http\Controllers\Selects\SelectPointsController;
use Src\Reminders\Http\Controllers\Selects\SelectRemindersController;
use Src\Reminders\Http\Controllers\Selects\SelectReminderStatusesController;
use Src\Reminders\Http\Controllers\Selects\SelectReminderTypesController;
use Src\Reminders\Http\Controllers\Selects\SelectRolesController;
use Src\Reminders\Http\Controllers\Selects\SelectSubjectsController;
use Src\Reminders\Http\Controllers\Selects\SelectSubjectTypesController;
use Src\Reminders\Http\Controllers\Selects\SelectUsersController;
use Src\Reminders\Http\Controllers\SwitchReminderStatusController;
use Src\Reminders\Http\Controllers\UpdateReminderController;
use Src\Reminders\Http\Controllers\UpdateReminderPageController;

Route::middleware(['web', 'auth'])->prefix('reminders')->name('reminders.')->group(function () {
    Route::get('/', ListRemindersPageController::class)->name('list-page');
    Route::post('/list', ListRemindersController::class)->name('list');
    Route::get('/create', CreateReminderPageController::class)->name('create-page');
    Route::post('/create', CreateReminderController::class)->name('create');
    Route::post('/delete', DeleteReminderController::class)->name('delete');

    Route::get('/logs', JournalReminderLogsPageController::class)->name('logs.list-page');
    Route::post('/logs', JournalReminderLogsController::class)->name('logs.list');

    Route::get('/{id}', UpdateReminderPageController::class)->name('update-page');
    Route::post('/{id}', UpdateReminderController::class)->name('update');
    Route::post('/{id}/switch-status', SwitchReminderStatusController::class)->name('switch-status');

    Route::get('/actions/select', SelectActionsController::class)->name('actions.select');
    Route::get('/cities/select', SelectCitiesController::class)->name('cities.select');
    Route::get('/points/select', SelectPointsController::class)->name('points.select');
    Route::get('/users/select', SelectUsersController::class)->name('users.select');
    Route::get('/roles/select', SelectRolesController::class)->name('roles.select');
    Route::get('/companies/select', SelectCompaniesController::class)->name('companies.select');
    Route::get('/subject-types/select', SelectSubjectTypesController::class)->name('subject_types.select');
    Route::get('/subjects/select', SelectSubjectsController::class)->name('subjects.select');
    Route::get('/reminders/select', SelectRemindersController::class)->name('reminders.select');
    Route::get('/types/select', SelectReminderTypesController::class)->name('reminder_types.select');
    Route::get('/statuses/select', SelectReminderStatusesController::class)->name('reminder_statuses.select');
});
