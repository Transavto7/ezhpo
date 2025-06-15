<?php

namespace Src\Notifications\Repositories;

use Src\Core\ValueObjects\Uuid;
use Src\Notifications\Entities\Notification;

interface NotificationRepository
{
    public function add(Notification $notification);

    public function update(Notification $notification);

    /**
     * @param string $id
     * @return Notification|null
     */
    public function findById(string $id): ?Notification;


    /**
     * @param Uuid[] $reminderIds
     * @return Uuid[]
     */
    public function getReminderIdsWithCompletedNotifications(array $reminderIds): array;

    /**
     * @param Uuid[] $reminderIds
     * @return Uuid[]
     */
    public function getReminderIdsWithViewedNotificationsByUser(array $reminderIds, int $userId): array;
}
