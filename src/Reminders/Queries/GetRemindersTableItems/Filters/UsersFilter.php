<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersTableItems\Filters;

use Src\Core\Filters\AbstractFilters\ArrayFilter;

final class UsersFilter extends ArrayFilter
{
    const NAME = 'users';

    protected $column = 'users.id';
}
