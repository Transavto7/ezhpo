<?php

declare(strict_types=1);

namespace Src\Notifications\Queries\GetNotificationTableItems\Filters;

use Src\Core\Filters\AbstractFilters\DateRangeFilter;

final class ExpiresAtFilter extends DateRangeFilter
{
    const NAME = 'expires_at';

    protected $column = 'notifications.expires_at';
}
