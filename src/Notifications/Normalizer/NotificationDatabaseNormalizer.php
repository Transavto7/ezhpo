<?php

namespace Src\Notifications\Normalizer;

use DateTimeImmutable;
use Src\Core\ValueObjects\Uuid;
use Src\Notifications\Entities\Notification;

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
            'read_at' => $notification->getReadAt(),
            'completed_at' => $notification->getCompletedAt(),
            'expires_at' => $notification->getExpiresAt(),
            'is_expired' => $notification->isExpired(),
            'created_at' => $notification->getCreatedAt(),
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
            $notification['reminder_id'] ? Uuid::fromString($notification['reminder_id']) : null,
            $notification['title'],
            $notification['content'],
            $notification['user_id'],
            $notification['initiator_user_id'],
            $notification['read_at'] ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $notification['read_at']) : null,
            $notification['completed_at'] ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $notification['completed_at']) : null,
            $notification['expires_at'] ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $notification['expires_at']) : null,
            DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $notification['created_at']),
        );
    }
}
