<?php

declare(strict_types=1);

namespace Src\Notifications\Queries\GetNotificationTableItems\Filters;

use Src\Core\Filters\AbstractFilters\StringFilter;

final class SearchFilter extends StringFilter
{
    const NAME = 'search';

    protected $column = 'notifications.title';
}
