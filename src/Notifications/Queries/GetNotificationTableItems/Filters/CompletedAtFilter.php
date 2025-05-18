<?php

declare(strict_types=1);

namespace Src\Notifications\Queries\GetNotificationTableItems\Filters;

use Src\Core\Filters\AbstractFilters\DateRangeFilter;

final class CompletedAtFilter extends DateRangeFilter
{
    const NAME = 'completed_at';

    protected $column = 'notifications.completed_at';
}
