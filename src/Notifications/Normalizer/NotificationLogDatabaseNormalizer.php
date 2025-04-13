<?php

namespace Src\Notifications\Normalizer;

use Src\Notifications\Entitites\NotificationLog;

final class NotificationLogDatabaseNormalizer
{
    /**
     * @param NotificationLog $notificationLog
     * @return array<string, string>
     */
    public function normalize(NotificationLog $notificationLog): array
    {
        return [
            'uuid' => $notificationLog->getId(),
            'notification_id' => $notificationLog->getNotificationId(),
            'user_id' => $notificationLog->getUserId(),
            'created_at' => $notificationLog->getCreatedAt(),
            'action' => $notificationLog->getType()
        ];
    }
}
