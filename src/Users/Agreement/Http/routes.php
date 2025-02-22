<?php

use Illuminate\Support\Facades\Route;
use Src\Users\Agreement\Http\Controllers\AcceptAgreementController;
use Src\Users\Agreement\Http\Controllers\AgreementPageController;

Route::middleware(['web', 'auth'])->prefix('agreement')->as('users.agreement')->group(function () {
    Route::get('/', AgreementPageController::class)->name('index');
    Route::post('/', AcceptAgreementController::class)->name('accept');
});
