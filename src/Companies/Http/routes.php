<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Companies\Http\Controllers\SyncDaDataCompanyController;

Route::middleware(['web', 'auth'])->prefix('companies')->as('companies.')->group(function () {
    Route::post('/{id}/sync-da-data', SyncDaDataCompanyController::class)->name('sync-da-data');
});
