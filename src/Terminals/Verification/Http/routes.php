<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Terminals\Verification\Http\Controllers\CheckVerificationCodeController;
use Src\Terminals\Verification\Http\Controllers\CreateVerificationController;
use Src\Terminals\Verification\Http\Controllers\RetrySendCodeController;

Route::middleware(['api', 'auth:api', 'update-last-connection'])->prefix('api/sdpo')->name('sdpo')->group(function () {
    Route::post('/verification', CreateVerificationController::class);
    Route::get('/verification', CheckVerificationCodeController::class);
    Route::post('/verification/retry', RetrySendCodeController::class);
});
