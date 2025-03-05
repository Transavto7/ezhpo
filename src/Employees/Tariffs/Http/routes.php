<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Employees\Tariffs\Http\Controllers\CreateTariffController;
use Src\Employees\Tariffs\Http\Controllers\CreateTariffPageController;
use Src\Employees\Tariffs\Http\Controllers\DeleteTariffController;
use Src\Employees\Tariffs\Http\Controllers\ListTariffController;
use Src\Employees\Tariffs\Http\Controllers\ListTariffPageController;
use Src\Employees\Tariffs\Http\Controllers\SelectPointsController;
use Src\Employees\Tariffs\Http\Controllers\SelectRolesController;
use Src\Employees\Tariffs\Http\Controllers\SelectTownsController;
use Src\Employees\Tariffs\Http\Controllers\UpdateTariffController;
use Src\Employees\Tariffs\Http\Controllers\UpdateTariffPageController;

Route::middleware(['web', 'auth'])->prefix('employees/tariffs')->name('employees.tariffs.')->group(function () {
    Route::get('/', ListTariffPageController::class)->name('list-page');
    Route::post('/list', ListTariffController::class)->name('list');
    Route::get('/create', CreateTariffPageController::class)->name('create-page');
    Route::post('/create', CreateTariffController::class)->name('create');
    Route::post('/delete', DeleteTariffController::class)->name('delete');
    Route::get('/{id}', UpdateTariffPageController::class)->name('update-page');
    Route::post('/{id}', UpdateTariffController::class)->name('update');
    Route::get('/towns/select', SelectTownsController::class)->name('towns.select');
    Route::get('/points/select', SelectPointsController::class)->name('points.select');
    Route::get('/roles/select', SelectRolesController::class)->name('roles.select');
});
