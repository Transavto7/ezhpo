<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersTableItems\Filters;

use Src\Core\Filters\AbstractFilters\ArrayFilter;

final class RemindersFilter extends ArrayFilter
{
    const NAME = 'reminders';

    protected $column = 'reminders.id';
}
