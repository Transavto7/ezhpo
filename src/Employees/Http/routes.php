<?php
declare(strict_types=1);


use Illuminate\Support\Facades\Route;
use Src\Employees\Http\Controllers\GetAllEmployeesListController;

Route::middleware(['auth:api', 'update-last-connection'])->prefix('/api/employees')->group(function () {
    Route::get('/list-all', GetAllEmployeesListController::class);
});
