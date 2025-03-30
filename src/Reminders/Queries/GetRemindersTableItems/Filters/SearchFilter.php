<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersTableItems\Filters;

use Src\Core\Filters\AbstractFilters\StringFilter;

final class SearchFilter extends StringFilter
{
    const NAME = 'search';

    protected $column = 'reminders.title';
}
