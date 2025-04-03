<?php

declare(strict_types=1);

namespace Src\Reminders;

use Illuminate\Support\ServiceProvider;
use Src\Reminders\Queries\GetReminderById\GetReminderRepositoryInterface;
use Src\Reminders\Queries\GetRemindersByContext\GetReminderByContextRepository;
use Src\Reminders\Repositories\Mysql\MysqlRemindersRepository;
use Src\Reminders\Repositories\RemindersRepository;

final class RemindersProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RemindersRepository::class, MysqlRemindersRepository::class);
        $this->app->bind(GetReminderRepositoryInterface::class, MysqlRemindersRepository::class);
        $this->app->bind(GetReminderByContextRepository::class, MysqlRemindersRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/Http/Views', 'reminders');
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadTranslationsFrom(__DIR__.'/Http/Lang', 'reminders');
    }
}
