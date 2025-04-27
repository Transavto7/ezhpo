<?php
declare(strict_types=1);

namespace Src\Notifications;

use InvalidArgumentException;
use Src\Notifications\SMSNotifier\SMSNotifier;
use Src\Notifications\TelegramNotifier\TelegramNotifier;

final class NotificationFactory
{
    public function create(string $type): Notifier
    {
        switch ($type) {
            case NotificationTypes::SMS:
                return app(SMSNotifier::class);
            case NotificationTypes::TELEGRAM:
                return app(TelegramNotifier::class);
            default:
                throw new InvalidArgumentException('Unknown notification type');
        }
    }
}
