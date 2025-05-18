<?php

declare(strict_types=1);

namespace Src\Notifications\Queries\GetNotificationTableItems\Filters;

use Src\Core\Filters\AbstractFilters\DateRangeFilter;

final class CreatedAtFilter extends DateRangeFilter
{
    const NAME = 'created_at';

    protected $column = 'notifications.created_at';
}
