<?php

declare(strict_types=1);

namespace Src\Employees\Holidays\Queries\GetHolidaysByYear;

use DateTimeImmutable;

final class GetHolidaysByYearQuery
{
    /** @var DateTimeImmutable */
    private $year;

    public function __construct(DateTimeImmutable $year)
    {
        $this->year = $year;
    }

    public function getYear(): DateTimeImmutable
    {
        return $this->year;
    }
}
