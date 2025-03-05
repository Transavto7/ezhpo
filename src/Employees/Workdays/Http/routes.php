<?php
declare(strict_types=1);


use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api', 'update-last-connection'])->prefix('/api/employees/workdays')->group(function () {

});
