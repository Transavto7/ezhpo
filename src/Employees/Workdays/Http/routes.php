<?php
declare(strict_types=1);

use App\Http\Middleware\StripEmptyParamsFromQueryString;
use Illuminate\Support\Facades\Route;
use Src\Employees\Workdays\Http\Controllers\CalcEmployeeSalariesController;
use Src\Employees\Workdays\Http\Controllers\StoreWorkdayController;
use Src\Employees\Workdays\Http\Controllers\WorkdayCreatePageController;
use Src\Employees\Workdays\Http\Controllers\WorkdayDeleteController;
use Src\Employees\Workdays\Http\Controllers\WorkdayDeleteManyController;
use Src\Employees\Workdays\Http\Controllers\WorkdaysIndexController;
use Src\Employees\Workdays\Http\Controllers\WorkdaysRegistrationController;
use Src\Employees\Workdays\Http\Controllers\WorkdaysReportController;

Route::middleware(['auth:api', 'update-last-connection'])->prefix('/api/sdpo/employees/workdays')->group(function () {
    Route::post('/registration', WorkdaysRegistrationController::class);
});

Route::middleware(['web', 'auth'])->prefix('employees/workdays')->name('employees.workdays.')->group(function () {
    Route::middleware(StripEmptyParamsFromQueryString::class)->get('/', WorkdaysIndexController::class)->name('index');
    Route::get('/report', WorkdaysReportController::class)->name('report');
    Route::get('/create', WorkdayCreatePageController::class)->name('create-page');
    Route::post('/', StoreWorkdayController::class)->name('store');
    Route::delete('{id}', WorkdayDeleteController::class)->name('trash');
    //TODO: а почему мы сделали это гет?
    Route::get('mass-trash', WorkdayDeleteManyController::class)->name('mass-trash');
    Route::get('/salaries/calc', CalcEmployeeSalariesController::class);
});
