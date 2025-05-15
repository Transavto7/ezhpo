<?php
declare(strict_types=1);

namespace Src\Terminals;


use Illuminate\Support\ServiceProvider;
use Src\Terminals\Commands\UpdateTerminalExaminationsCountCommand;

final class TerminalsProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Http/Views', 'terminals');
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                UpdateTerminalExaminationsCountCommand::class
            ]);
        }
    }
}
