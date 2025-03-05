<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Queries\GetTariffsTableItems\Filters;

use Src\Core\Filters\AbstractFilters\ArrayFilter;

final class TownsFilter extends ArrayFilter
{
    const NAME = 'towns';

    protected $column = 'town_id';
}
