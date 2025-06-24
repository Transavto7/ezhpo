<?php
declare(strict_types=1);

namespace Src\DocsGeneration;

use Illuminate\Support\ServiceProvider;

final class DocsGenerationProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/Http/Views', 'docs');
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
    }
}
