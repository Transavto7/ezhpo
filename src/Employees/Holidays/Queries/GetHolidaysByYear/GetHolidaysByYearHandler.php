<?php

declare(strict_types=1);

namespace Src\Employees\Holidays\Queries\GetHolidaysByYear;

use Src\Employees\Holidays\Eloquent\Holiday;

final class GetHolidaysByYearHandler
{
    public function handle(GetHolidaysByYearQuery $query): array
    {
        return Holiday::query()->whereYear('date', $query->getYear()->format('Y'))->pluck('date')->toArray();
    }
}
