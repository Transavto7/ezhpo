<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersTableItems\Filters;

use Src\Core\Filters\AbstractFilters\StringFilter;

final class SubjectTypeFilter extends StringFilter
{
    const NAME = 'subject_type';

    public function apply($query)
    {
        $query->whereRaw('JSON_EXTRACT(context, "$.subject_type") = ?', [$this->value]);
    }
}
