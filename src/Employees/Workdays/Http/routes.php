<?php
declare(strict_types=1);


use Illuminate\Support\Facades\Route;
use Src\Employees\Workdays\Http\Controllers\WorkdaysRegistrationController;

Route::middleware(['auth:api', 'update-last-connection'])->prefix('/api/sdpo/employees/workdays')->group(function () {
    Route::post('/registration', WorkdaysRegistrationController::class);
});
