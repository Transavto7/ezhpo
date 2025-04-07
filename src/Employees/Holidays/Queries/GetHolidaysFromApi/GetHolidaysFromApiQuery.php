<?php

declare(strict_types=1);

namespace Src\Employees\Holidays\Queries\GetHolidaysFromApi;

final class GetHolidaysFromApiQuery
{
    /** @var int */
    private $year;

    /**
     * @param int $year
     */
    public function __construct(int $year)
    {
        $this->year = $year;
    }

    public function getYear(): int
    {
        return $this->year;
    }
}
