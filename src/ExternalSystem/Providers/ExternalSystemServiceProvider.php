<?php

namespace Src\ExternalSystem\Providers;

use Illuminate\Support\ServiceProvider;
use Src\ExternalSystem\Services\ExternalSystemSendService;
use Src\ExternalSystem\Services\ExternalSystemSendServiceInterface;
use Src\ExternalSystem\Services\Terminology\TerminologyService;

final class ExternalSystemServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ExternalSystemSendServiceInterface::class, ExternalSystemSendService::class);

        $this->app->bind('terminology',function() {
            return new TerminologyService();
        });
    }

    public function boot() {
        $this->loadViewsFrom(base_path('src/ExternalSystem/Views'), 'external-system');
        $this->loadRoutesFrom(base_path('src/ExternalSystem/Http/external-system.php'));
    }
}
