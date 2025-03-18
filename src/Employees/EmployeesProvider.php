<?php

declare(strict_types=1);

namespace Src\Employees;

use Illuminate\Support\ServiceProvider;
use Src\Employees\Holidays\HolidayProvider;
use Src\Employees\Workdays\WorkdaysProvider;

final class EmployeesProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(WorkdaysProvider::class);
        $this->app->register(HolidayProvider::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
    }
}
