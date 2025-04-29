<?php
declare(strict_types=1);

namespace Src\Verification;

use Illuminate\Support\ServiceProvider;
use Src\Verification\Repositories\VerificationPostgresRepository;
use Src\Verification\Repositories\VerificationRepository;

final class VerificationProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(VerificationRepository::class, VerificationPostgresRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
    }
}
