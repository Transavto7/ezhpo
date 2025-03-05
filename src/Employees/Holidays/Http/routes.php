<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Employees\Holidays\Http\Controllers\GetHolidaysByYearController;
use Src\Employees\Holidays\Http\Controllers\GetHolidaysFromApiController;
use Src\Employees\Holidays\Http\Controllers\HolidaysIndexController;
use Src\Employees\Holidays\Http\Controllers\SaveHolidaysController;

Route::middleware(['web', 'auth'])->prefix('employees/holidays')->name('employees.holidays.')->group(function () {
    Route::get('/', HolidaysIndexController::class)->name('index');
    Route::post('/', SaveHolidaysController::class)->name('save');
    Route::get('/by-year', GetHolidaysByYearController::class)->name('by-year');
    Route::get('/from-api', GetHolidaysFromApiController::class)->name('from-api');
});
