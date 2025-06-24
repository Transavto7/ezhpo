<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\DocsGeneration\Http\Controllers\DeleteDocController;
use Src\DocsGeneration\Http\Controllers\GetDocPageController;
use Src\DocsGeneration\Http\Controllers\GetDocPdfController;
use Src\DocsGeneration\Http\Controllers\SetDocPdfController;
use Src\DocsGeneration\Http\Controllers\UpdateDocController;

Route::middleware(['web', 'auth'])->prefix('docs')->as('docs.')->group(function () {
    Route::get('{type}/{anketa_id}/pdf', GetDocPdfController::class)->name('get.pdf');
    Route::post('{type}/{anketa_id}/set', SetDocPdfController::class)->name('add.pdf');
    Route::any('{type}/{anketa_id}/delete', DeleteDocController::class)->name('delete');
    Route::get('{type}/{anketa_id}', GetDocPageController::class)->name('get');
});

Route::middleware('auth:api')->prefix('/api')->group(function () {
    Route::put('/update-doc/{type}', UpdateDocController::class)->name('docs.update');
});
