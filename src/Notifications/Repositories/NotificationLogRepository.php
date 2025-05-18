<?php

namespace Src\Notifications\Repositories;

use Src\Notifications\Entities\NotificationLog;

interface NotificationLogRepository
{
    public function add(NotificationLog $notificationLog);
}
