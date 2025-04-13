<?php

declare(strict_types=1);

namespace Src\Notifications;

use Illuminate\Support\ServiceProvider;
use Src\Notifications\Queries\GetUnreadUserNotifications\GetUnreadUserNotificationsRepository;
use Src\Notifications\Repository\Mysql\MysqlNotificationLogRepository;
use Src\Notifications\Repository\Mysql\MysqlNotificationRepository;
use Src\Notifications\Repository\NotificationLogRepository;
use Src\Notifications\Repository\NotificationRepository;

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
        $this->loadTranslationsFrom(__DIR__.'/Http/Lang', 'notifications');
    }
}
