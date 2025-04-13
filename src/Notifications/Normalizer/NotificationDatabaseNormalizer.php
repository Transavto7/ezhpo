<?php

namespace Src\Notifications\Normalizer;

use DateTimeImmutable;
use Src\Core\ValueObjects\Uuid;
use Src\Notifications\Entitites\Notification;

final class NotificationDatabaseNormalizer
{
    /**
     * @param Notification $notification
     * @return array<string, string>
     */
    public function normalize(Notification $notification): array
    {
        return [
            'id' => $notification->getId(),
            'title' => $notification->getTitle(),
            'content' => $notification->getContent(),
            'reminder_id' => $notification->getReminderId(),
            'user_id' => $notification->getRecipientId(),
            'initiator_user_id' => $notification->getSenderId(),
            'expires_at' => $notification->getExpiresAt(),
            'is_expired' => $notification->isExpired(),
            'created_at' => $notification->getCreatedAt(),
            'viewed_at' => $notification->getViewedAt(),
            'completed_at' => $notification->getCompletedAt()
        ];
    }

    /**
     * @param array<string, string> $notification
     * @return Notification
     */
    public function denormalize(array $notification): Notification
    {
        return new Notification(
            Uuid::fromString($notification['id']),
            DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $notification['created_at']),
            $notification['reminder_id'] ? Uuid::fromString($notification['reminder_id']) : null,
            $notification['title'],
            $notification['content'],
            $notification['user_id'],
            $notification['initiator_user_id'],
            $notification['expires_at'] ? DateTimeImmutable::createFromFormat('Y-m-d H:i', $notification['expires_at']) : null,
            $notification['viewed_at'] ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $notification['viewed_at']) : null,
            $notification['completed_at'] ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $notification['completed_at']) : null,
        );
    }
}
