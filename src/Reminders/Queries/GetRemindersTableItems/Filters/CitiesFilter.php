<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersTableItems\Filters;

use Src\Core\Filters\AbstractFilters\ArrayFilter;

final class CitiesFilter extends ArrayFilter
{
    const NAME = 'cities';

    protected $column = 'towns.id';
}
