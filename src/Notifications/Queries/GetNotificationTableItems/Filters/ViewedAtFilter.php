<?php

declare(strict_types=1);

namespace Src\Notifications\Queries\GetNotificationTableItems\Filters;

use Src\Core\Filters\AbstractFilters\DateRangeFilter;

final class ViewedAtFilter extends DateRangeFilter
{
    const NAME = 'viewed_at';

    protected $column = 'notifications.viewed_at';
}
