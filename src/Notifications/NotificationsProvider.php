<?php

declare(strict_types=1);

namespace Src\Notifications;

use Illuminate\Bus\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Src\Notifications\Commands\CreateNotificationsByContext\CreateNotificationsByContextCommand;
use Src\Notifications\Commands\CreateNotificationsByContext\CreateNotificationsByContextHandler;
use Src\Notifications\Commands\LogNotificationActivity\LogNotificationActivityCommand;
use Src\Notifications\Commands\LogNotificationActivity\LogNotificationActivityHandler;
use Src\Notifications\Queries\GetUnreadUserNotifications\GetUnreadUserNotificationsRepository;
use Src\Notifications\Repositories\Mysql\MysqlNotificationLogRepository;
use Src\Notifications\Repositories\Mysql\MysqlNotificationRepository;
use Src\Notifications\Repositories\NotificationLogRepository;
use Src\Notifications\Repositories\NotificationRepository;

final class NotificationsProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NotificationRepository::class, MysqlNotificationRepository::class);
        $this->app->bind(NotificationLogRepository::class, MysqlNotificationLogRepository::class);
        $this->app->bind(GetUnreadUserNotificationsRepository::class, MysqlNotificationRepository::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/Http/Views', 'Notifications');
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        $this->app->extend(Dispatcher::class, function (Dispatcher $dispatcher) {
            $dispatcher->map([
                CreateNotificationsByContextCommand::class => CreateNotificationsByContextHandler::class,
                LogNotificationActivityCommand::class => LogNotificationActivityHandler::class,
            ]);

            return $dispatcher;
        });
    }
}
