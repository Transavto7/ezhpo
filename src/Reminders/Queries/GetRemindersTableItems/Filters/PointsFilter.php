<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersTableItems\Filters;

use Src\Core\Filters\AbstractFilters\ArrayFilter;

final class PointsFilter extends ArrayFilter
{
    const NAME = 'points';

    protected $column = 'points.id';
}
