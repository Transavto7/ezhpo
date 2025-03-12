<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Employees\Workdays\Http\Controllers\CalcEmployeeSalariesController;
use Src\Employees\Workdays\Http\Controllers\StoreWorkdayController;
use Src\Employees\Workdays\Http\Controllers\WorkdayCreatePageController;
use Src\Employees\Workdays\Http\Controllers\WorkdaysJournalController;
use Src\Employees\Workdays\Http\Controllers\WorkdaysRegistrationController;
use Src\Employees\Workdays\Http\Controllers\WorkdaysReportController;

Route::middleware(['auth:api', 'update-last-connection'])->prefix('/api/sdpo/employees/workdays')->group(function () {
    Route::post('/registration', WorkdaysRegistrationController::class);
});

Route::middleware(['auth:web'])->prefix('/employees/workdays')->name('employees.workdays.')->group(function () {
    Route::get('/journal', WorkdaysJournalController::class)->name('journal');
    Route::get('/report', WorkdaysReportController::class)->name('report');
    Route::get('/', WorkdayCreatePageController::class)->name('create-page');
    Route::post('/', StoreWorkdayController::class)->name('store');

    Route::get('/salaries/calc', CalcEmployeeSalariesController::class);
});

Route::prefix('/employees/workdays')->group(function () {
    Route::get('/salaries/calc', CalcEmployeeSalariesController::class);
});
