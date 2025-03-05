<?php

declare(strict_types=1);

namespace Src\Employees;

use Illuminate\Support\ServiceProvider;
use Src\Employees\Workdays\WorkdaysProvider;

final class EmployeesProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->register(WorkdaysProvider::class);
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
    }
}
