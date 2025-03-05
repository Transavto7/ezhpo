<?php

declare(strict_types=1);

namespace Src\Employees\Workdays;

use Illuminate\Support\ServiceProvider;
use Src\Employees\Workdays\Providers\WorkdaysEventServiceProvider;

final class WorkdaysProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(WorkdaysEventServiceProvider::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->loadViewsFrom(__DIR__.'/Http/Views', 'Workdays');
    }
}
