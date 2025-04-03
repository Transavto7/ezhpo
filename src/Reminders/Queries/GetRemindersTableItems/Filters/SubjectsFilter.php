<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersTableItems\Filters;

use Illuminate\Database\Query\Builder;
use Src\Core\Filters\AbstractFilters\ArrayFilter;

final class SubjectsFilter extends ArrayFilter
{
    const NAME = 'subjects';

    public function apply($query)
    {
        $query->where(function (Builder $query) {
            $query->whereIn('subject_drivers.id', $this->value)
                ->orWhereIn('subject_cars.id', $this->value);
        });
    }
}
