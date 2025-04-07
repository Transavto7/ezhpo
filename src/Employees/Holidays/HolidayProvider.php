<?php

declare(strict_types=1);

namespace Src\Employees\Holidays;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class HolidayProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(HttpClientInterface::class, NativeHttpClient::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->loadViewsFrom(__DIR__.'/Http/Views', 'Holidays');
    }
}
