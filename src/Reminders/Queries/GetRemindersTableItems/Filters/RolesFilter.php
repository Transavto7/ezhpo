<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersTableItems\Filters;

use Src\Core\Filters\AbstractFilters\ArrayFilter;

final class RolesFilter extends ArrayFilter
{
    const NAME = 'roles';

    protected $column = 'roles.id';
}
