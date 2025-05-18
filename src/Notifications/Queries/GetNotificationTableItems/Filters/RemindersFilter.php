<?php

declare(strict_types=1);

namespace Src\Notifications\Queries\GetNotificationTableItems\Filters;

use Src\Core\Filters\AbstractFilters\ArrayFilter;

final class RemindersFilter extends ArrayFilter
{
    const NAME = 'reminders';

    protected $column = 'notifications.reminder_id';
}
