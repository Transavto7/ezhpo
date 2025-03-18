<?php
declare(strict_types=1);

namespace Src\Employees\Holidays;

use Illuminate\Support\ServiceProvider;

final class HolidayProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->loadViewsFrom(__DIR__.'/Http/Views', 'Holidays');
    }
}
