<?php

declare(strict_types=1);

namespace Src\Notifications\Queries\GetNotificationTableItems\Filters;

use Src\Notifications\Queries\GetNotificationTableItems\NotificationsStatusFilter;

final class StatusFilter extends NotificationsStatusFilter
{
    const NAME = 'status';
}
