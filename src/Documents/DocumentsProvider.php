<?php

declare(strict_types=1);

namespace Src\Documents;

use Illuminate\Support\ServiceProvider;

final class DocumentsProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
        $this->loadViewsFrom(__DIR__ . '/Http/Views', 'Documents');
    }
}
