<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Queries\GetTariffsTableItems\Filters;

use Src\Core\Filters\AbstractFilters\ArrayFilter;

final class RolesFilter extends ArrayFilter
{
    const NAME = 'roles';

    protected $column = 'role_id';
}
