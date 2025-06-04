<?php

declare(strict_types=1);

namespace Src\Notifications\Queries\GetNotificationTableItems\Filters;

use Src\Core\Filters\AbstractFilters\DateRangeFilter;

final class ReadAtFilter extends DateRangeFilter
{
    const NAME = 'read_at';

    protected $column = 'notifications.read_at';
}
