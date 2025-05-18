<?php

namespace Src\Notifications\Queries\GetUnreadUserNotifications;

interface GetUnreadUserNotificationsRepository
{
    /**
     * @param int $userId
     * @return NotificationViewModel[]
     */
    public function getUnread(int $userId): array;
}
