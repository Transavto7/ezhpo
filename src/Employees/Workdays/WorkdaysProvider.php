<?php
declare(strict_types=1);

namespace Src\Employees\Workdays;

use Illuminate\Support\ServiceProvider;

final class WorkdaysProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
    }
}
