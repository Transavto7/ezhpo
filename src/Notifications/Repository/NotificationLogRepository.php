<?php

namespace Src\Notifications\Repository;

use Src\Notifications\Entitites\NotificationLog;

interface NotificationLogRepository
{
    public function add(NotificationLog $notificationLog);
}
