<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Companies\Http\Controllers\CheckForCompanyDebtsController;
use Src\Companies\Http\Controllers\GetCompanyDebtController;
use Src\Companies\Http\Controllers\SyncDaDataCompanyController;

Route::middleware(['web', 'auth'])->prefix('companies')->as('companies.')->group(function () {
    Route::post('/{id}/sync-da-data', SyncDaDataCompanyController::class)->name('sync-da-data');
    Route::post('/{id}/check-for-debts', CheckForCompanyDebtsController::class)->name('check-for-debts');
    Route::get('/{id}/get-debt', GetCompanyDebtController::class)->name('get-debt');
});
