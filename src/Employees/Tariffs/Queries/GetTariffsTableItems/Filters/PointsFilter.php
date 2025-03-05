<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Queries\GetTariffsTableItems\Filters;

use Src\Core\Filters\AbstractFilters\ArrayFilter;

final class PointsFilter extends ArrayFilter
{
    const NAME = 'points';

    protected $column = 'point_id';
}
