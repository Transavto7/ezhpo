<?php
declare(strict_types=1);


use Illuminate\Support\Facades\Route;
use Src\Employees\Http\Controllers\GetAllEmployeesListController;
use Src\Employees\Http\Controllers\GetEmployeeController;

Route::middleware(['auth:api', 'update-last-connection'])->prefix('/api/sdpo/employees')->group(function () {
    Route::get('/', GetAllEmployeesListController::class);
    Route::get('/{hash_id}/', GetEmployeeController::class);
});
