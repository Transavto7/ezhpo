<?php
declare(strict_types=1);

namespace Src\Employees\WorkdaysJournal;

use Illuminate\Support\ServiceProvider;

final class WorkdaysJournalProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
        $this->loadViewsFrom(__DIR__ . '/Http/Views', 'WorkdaysJournal');
    }
}
