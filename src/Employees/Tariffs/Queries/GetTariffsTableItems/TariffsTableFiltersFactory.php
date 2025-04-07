<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Queries\GetTariffsTableItems;

use Src\Core\Filters\FilterFactory;
use Src\Employees\Tariffs\Queries\GetTariffsTableItems\Filters\PointsFilter;
use Src\Employees\Tariffs\Queries\GetTariffsTableItems\Filters\RolesFilter;
use Src\Employees\Tariffs\Queries\GetTariffsTableItems\Filters\SearchFilter;
use Src\Employees\Tariffs\Queries\GetTariffsTableItems\Filters\TownsFilter;

final class TariffsTableFiltersFactory extends FilterFactory
{
    protected $filterClasses = [
        SearchFilter::NAME => SearchFilter::class,
        TownsFilter::NAME => TownsFilter::class,
        RolesFilter::NAME => RolesFilter::class,
        PointsFilter::NAME => PointsFilter::class,
    ];
}
