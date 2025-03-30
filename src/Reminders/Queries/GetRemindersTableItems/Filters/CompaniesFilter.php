<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersTableItems\Filters;

use Src\Core\Filters\AbstractFilters\ArrayFilter;

final class CompaniesFilter extends ArrayFilter
{
    const NAME = 'companies';

    protected $column = 'companies.id';
}
