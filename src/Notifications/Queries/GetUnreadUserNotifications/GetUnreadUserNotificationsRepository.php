<?php

namespace Src\Notifications\Queries\GetUnreadUserNotifications;

use Src\Notifications\Queries\NotificationViewModel;

interface GetUnreadUserNotificationsRepository
{
    /**
     * @param int $userId
     * @return NotificationViewModel[]
     */
    public function getUnread(int $userId): array;
}
