<?php

namespace App\Providers;

use App\Services\Unleash\UnleashClient;
use Illuminate\Support\ServiceProvider;

final class UnleashServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(UnleashClient::class, function () {
            return new UnleashClient();
        });
    }
}
