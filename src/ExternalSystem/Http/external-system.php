<?php

use Src\ExternalSystem\Http\Controllers\ExternalSystemSendController;
use Src\ExternalSystem\Http\Controllers\ExternalSystemUploadEcpController;

//Route::group(['middleware' => 'auth:api', 'prefix' => 'external-system', 'as' => 'external-system.'], function () {
Route::group(['prefix' => 'external-system', 'as' => 'external-system.'], function () {
    Route::get('/send/{anketaId}', ExternalSystemSendController::class)->name('send');
});
