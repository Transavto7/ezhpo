<?php

namespace Src\Notifications\Entities;

use App\User;
use Src\Core\ValueObjects\Uuid;
use Src\Reminders\ValueObjects\ReminderByContext;

class NotificationsBuilder
{
    public static function fromReminder(ReminderByContext $reminderByContext, \DateTimeImmutable $createdAt, User $recipient, ?User $sender): Notification
    {
        return new Notification(
            Uuid::next(),
            $reminderByContext->getId(),
            $reminderByContext->getName(),
            $reminderByContext->getContent(),
            $recipient->getAttribute('id'),
            $sender ? $sender->getAttribute('id') : null,
            null,
            null,
            null,
            $reminderByContext->getExpiresAt(),
            $createdAt,
        );
    }
}
