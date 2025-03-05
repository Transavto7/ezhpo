<?php

declare(strict_types=1);

use App\Http\Middleware\StripEmptyParamsFromQueryString;
use Illuminate\Support\Facades\Route;
use Src\Employees\Workdays\Http\Controllers\CalcEmployeeSalariesController;
use Src\Employees\Workdays\Http\Controllers\GetAllEmployeesListController;
use Src\Employees\Workdays\Http\Controllers\GetEmployeeController;
use Src\Employees\Workdays\Http\Controllers\StoreWorkdayController;
use Src\Employees\Workdays\Http\Controllers\WorkdayCreatePageController;
use Src\Employees\Workdays\Http\Controllers\WorkdayDeleteController;
use Src\Employees\Workdays\Http\Controllers\WorkdayDeleteManyController;
use Src\Employees\Workdays\Http\Controllers\WorkdaysIndexController;
use Src\Employees\Workdays\Http\Controllers\WorkdaysRegistrationController;
use Src\Employees\Workdays\Http\Controllers\WorkdaysReportController;

Route::middleware(['auth:api', 'update-last-connection'])->prefix('/api/sdpo/employees')->group(function () {
    Route::get('/', GetAllEmployeesListController::class);
    Route::get('/{hash_id}/', GetEmployeeController::class);

    Route::prefix('workdays')->group(function () {
        Route::post('/registration', WorkdaysRegistrationController::class);
    });
});

Route::middleware(['web', 'auth'])->prefix('employees/workdays')->name('employees.workdays.')->group(function () {
    Route::middleware(StripEmptyParamsFromQueryString::class)->get('/', WorkdaysIndexController::class)->name('index');
    Route::get('/report', WorkdaysReportController::class)->name('report');
    Route::get('/create', WorkdayCreatePageController::class)->name('create-page');
    Route::post('/', StoreWorkdayController::class)->name('store');
    Route::get('/trash/{id}', WorkdayDeleteController::class)->name('trash');
    Route::get('/mass-trash', WorkdayDeleteManyController::class)->name('mass-trash');
    Route::post('/salaries/calc', CalcEmployeeSalariesController::class);
});
