<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Reminders\Http\Controllers\CreateReminderController;
use Src\Reminders\Http\Controllers\CreateReminderPageController;
use Src\Reminders\Http\Controllers\DeleteReminderController;
use Src\Reminders\Http\Controllers\ExecuteReminderLogController;
use Src\Reminders\Http\Controllers\GetRemindersByContextController;
use Src\Reminders\Http\Controllers\GetUnreadRemindersController;
use Src\Reminders\Http\Controllers\JournalReminderLogsController;
use Src\Reminders\Http\Controllers\JournalReminderLogsPageController;
use Src\Reminders\Http\Controllers\ListRemindersController;
use Src\Reminders\Http\Controllers\ListRemindersPageController;
use Src\Reminders\Http\Controllers\Selects\SelectActionsController;
use Src\Reminders\Http\Controllers\Selects\SelectCitiesController;
use Src\Reminders\Http\Controllers\Selects\SelectCompaniesController;
use Src\Reminders\Http\Controllers\Selects\SelectPointsController;
use Src\Reminders\Http\Controllers\Selects\SelectRolesController;
use Src\Reminders\Http\Controllers\Selects\SelectSubjectsController;
use Src\Reminders\Http\Controllers\Selects\SelectSubjectTypesController;
use Src\Reminders\Http\Controllers\Selects\SelectUsersController;
use Src\Reminders\Http\Controllers\ShowReminderLogController;
use Src\Reminders\Http\Controllers\UpdateReminderController;
use Src\Reminders\Http\Controllers\UpdateReminderPageController;

Route::middleware(['web', 'auth'])->prefix('reminders')->name('reminders.')->group(function () {
    Route::get('/', ListRemindersPageController::class)->name('list-page');
    Route::post('/list', ListRemindersController::class)->name('list');
    Route::get('/create', CreateReminderPageController::class)->name('create-page');
    Route::post('/create', CreateReminderController::class)->name('create');
    Route::post('/delete', DeleteReminderController::class)->name('delete');

    Route::post('/by-context', GetRemindersByContextController::class)->name('by-context');

    Route::get('/{id}', UpdateReminderPageController::class)->name('update-page');
    Route::post('/{id}', UpdateReminderController::class)->name('update');

    Route::get('/actions/select', SelectActionsController::class)->name('actions.select');
    Route::get('/cities/select', SelectCitiesController::class)->name('cities.select');
    Route::get('/points/select', SelectPointsController::class)->name('points.select');
    Route::get('/users/select', SelectUsersController::class)->name('users.select');
    Route::get('/roles/select', SelectRolesController::class)->name('roles.select');
    Route::get('/companies/select', SelectCompaniesController::class)->name('companies.select');
    Route::get('/subject-types/select', SelectSubjectTypesController::class)->name('subject_types.select');
    Route::get('/subjects/select', SelectSubjectsController::class)->name('subjects.select');

    Route::post('/show-reminder-modal/{id}', ShowReminderLogController::class)->name('modal.show');
    Route::post('/complete-reminder-modal/{id}', ExecuteReminderLogController::class)->name('modal.complete');
    Route::get('/modal/unread-reminders-modal', GetUnreadRemindersController::class)->name('modal.get-unread');
    Route::get('/log/show', JournalReminderLogsPageController::class)->name('log.journal');
    Route::post('/log/show', JournalReminderLogsController::class)->name('log.journal-data');
});
