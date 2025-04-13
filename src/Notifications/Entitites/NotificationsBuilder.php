<?php

namespace Src\Notifications\Entitites;

use App\User;
use Src\Core\ValueObjects\Uuid;
use Src\Reminders\Queries\GetRemindersByContext\ReminderByContext;

class NotificationsBuilder
{
    public static function fromReminder(ReminderByContext $reminderByContext, \DateTimeImmutable $createdAt, User $recipient, ?User $sender): Notification
    {
        return new Notification(
            Uuid::next(),
            $createdAt,
            $reminderByContext->getId(),
            $reminderByContext->getName(),
            $reminderByContext->getContent(),
            $recipient->getAttribute('id'),
            $sender ? $sender->getAttribute('id') : null,
            $reminderByContext->getExpiresAt(),
            null,
            null,
        );
    }
}
