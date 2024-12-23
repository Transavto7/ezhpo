<?php

namespace App\Providers;

use App\Services\Unleash\OfflineUnleash;
use Illuminate\Support\ServiceProvider;
use Throwable;
use Unleash\Client\Unleash;
use Unleash\Client\UnleashBuilder;

final class UnleashServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Unleash::class, function () {
            $offlineUnleash = new OfflineUnleash();

            if (!config('unleash.enabled')) {
                return $offlineUnleash;
            }

            try {
                return UnleashBuilder::create()
                    ->withAppName(config('unleash.app-name'))
                    ->withAppUrl(config('unleash.app-url'))
                    ->withInstanceId(config('unleash.instance-id'))
                    ->withHeader('Authorization', config('unleash.token'))
                    ->withInstanceId(config('unleash.instance-id'))
                    ->build();
            } catch (Throwable $exception) {

            }

            return $offlineUnleash;
        });
    }
}
