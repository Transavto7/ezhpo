<?php
declare(strict_types=1);

namespace Src\Companies;

use Illuminate\Support\ServiceProvider;

final class CompaniesProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
    }
}
