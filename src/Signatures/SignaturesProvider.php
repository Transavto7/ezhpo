<?php

declare(strict_types=1);

namespace Src\Signatures;

use Illuminate\Support\ServiceProvider;
use Src\Signatures\Services\SignatureService;
use Src\Signatures\Services\SignatureWorker;

final class SignaturesProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(SignatureWorker::NAME, function () {
            return new SignatureService();
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
    }
}
