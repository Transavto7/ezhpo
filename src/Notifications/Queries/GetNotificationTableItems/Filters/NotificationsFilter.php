<?php

declare(strict_types=1);

namespace Src\Notifications\Queries\GetNotificationTableItems\Filters;

use Src\Core\Filters\AbstractFilters\ArrayFilter;

final class NotificationsFilter extends ArrayFilter
{
    const NAME = 'notifications';

    protected $column = 'notifications.id';
}
