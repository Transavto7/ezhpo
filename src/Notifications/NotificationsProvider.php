<?php

declare(strict_types=1);

namespace Src\Notifications;

use Illuminate\Support\ServiceProvider;
use Src\Notifications\SMSNotifier\SMSNotifier;
use Src\Notifications\SMSNotifier\SMSNotifierMock;
use Src\Notifications\TelegramNotifier\TelegramNotifier;
use Src\Notifications\TelegramNotifier\TelegramNotifierMock;

final class NotificationsProvider extends ServiceProvider
{
    public function register()
    {
        if (config('app.env') !== 'production') {
            $this->app->bind(SMSNotifier::class, SMSNotifierMock::class);
            $this->app->bind(TelegramNotifier::class, TelegramNotifierMock::class);
        }
    }
}
