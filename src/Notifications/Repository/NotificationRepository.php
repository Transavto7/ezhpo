<?php

namespace Src\Notifications\Repository;

use Src\Notifications\Entitites\Notification;

interface NotificationRepository
{
    public function add(Notification $notification);

    public function update(Notification $notification);

    /**
     * @param string $id
     * @return Notification|null
     */
    public function findById(string $id): ?Notification;
}
