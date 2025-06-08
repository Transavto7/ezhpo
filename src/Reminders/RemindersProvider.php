<?php

declare(strict_types=1);

namespace Src\Reminders;

use Illuminate\Bus\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Src\Reminders\Commands\SwitchReminderStatus\SwitchReminderStatusCommand;
use Src\Reminders\Commands\SwitchReminderStatus\SwitchReminderStatusHandler;
use Src\Reminders\Queries\GetReminderById\GetReminderRepositoryInterface;
use Src\Reminders\Repositories\GetRemindersByContextRepository;
use Src\Reminders\Repositories\Mysql\GetRemindersByContextMysqlRepository;
use Src\Reminders\Repositories\Mysql\MysqlRemindersRepository;
use Src\Reminders\Repositories\RemindersRepository;

final class RemindersProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RemindersRepository::class, MysqlRemindersRepository::class);
        $this->app->bind(GetReminderRepositoryInterface::class, MysqlRemindersRepository::class);
        $this->app->bind(GetRemindersByContextRepository::class, GetRemindersByContextMysqlRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/Http/Views', 'Reminders');
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadTranslationsFrom(__DIR__.'/Http/Lang', 'reminders');

        $this->app->extend(Dispatcher::class, function (Dispatcher $dispatcher) {
            $dispatcher->map([
                SwitchReminderStatusCommand::class => SwitchReminderStatusHandler::class,
            ]);

            return $dispatcher;
        });
    }
}
