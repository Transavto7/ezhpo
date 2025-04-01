<?php

declare(strict_types=1);

namespace Src\Companies;

use Illuminate\Support\ServiceProvider;
use Src\Companies\Console\SyncCompaniesDebtsCommand;

final class CompaniesProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncCompaniesDebtsCommand::class,
            ]);
        }

        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
    }
}
