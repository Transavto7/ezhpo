<?php

use Illuminate\Support\Facades\Route;
use Src\Documents\Http\Controllers\DocumentsPageController;
use Src\Documents\Http\Controllers\GenerateDocumentToSignController;

Route::get('/documents/generate-pdf', GenerateDocumentToSignController::class);
Route::get('/documents', DocumentsPageController::class)->name('documents.index');
