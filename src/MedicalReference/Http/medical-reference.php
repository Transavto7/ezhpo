<?php

use Src\MedicalReference\Http\Controllers\MedicalReferenceDownloadController;

//Route::group(['middleware' => 'auth:api', 'prefix' => 'external-system', 'as' => 'external-system.'], function () {
Route::group(['prefix' => 'medical-reference', 'as' => 'medical-reference.'], function () {
    Route::get('/download/{anketaId}', MedicalReferenceDownloadController::class)->name('download');
});
