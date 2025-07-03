<?php
declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Signatures\Http\Controllers\GetSignatureController;
use Src\Signatures\Http\Controllers\UploadSignatureController;

Route::middleware(['web', 'auth'])->prefix('docs')->as('signatures.')->group(function () {
    Route::post('{type}/{fromId}/signature/upload', UploadSignatureController::class)->name('upload');
    Route::get('{type}/{fromId}/signature/download', GetSignatureController::class)->name('download');
});
