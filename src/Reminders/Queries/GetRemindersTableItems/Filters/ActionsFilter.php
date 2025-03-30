<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersTableItems\Filters;

use Src\Core\Filters\AbstractFilters\ArrayFilter;

final class ActionsFilter extends ArrayFilter
{
    const NAME = 'actions';

    protected $column = 'reminders.action';
}
