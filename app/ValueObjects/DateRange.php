<?php

namespace App\ValueObjects;

use App\Exceptions\DateRangeParseFailedException;
use Carbon\Carbon;

final class DateRange
{
    const RANGE_SEPARATOR = '—';

    /**
     * @var Carbon|null
     */
    private $dateStart;
    /**
     * @var Carbon|null
     */
    private $dateEnd;

    /**
     * @param Carbon|null $dateStart
     * @param Carbon|null $dateEnd
     */
    private function __construct(?Carbon $dateStart, ?Carbon $dateEnd)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
    }

    /**
     * @param string $value
     * @return DateRange
     * @throws DateRangeParseFailedException
     */
    public static function from(string $value): DateRange
    {
        $exploded = explode(self::RANGE_SEPARATOR, $value);

        if (count($exploded) < 1 || count($exploded) > 2) {
            throw new DateRangeParseFailedException("Invalid date range '$value' format");
        }

        $dateStart = new Carbon($exploded[0]);
        $dateEnd = null;

        if (count($exploded) == 2) {
            $dateEnd = new Carbon($exploded[1]);
        }

        return new self($dateStart, $dateEnd);
    }

    public function getDateStart(): ?Carbon
    {
        return $this->dateStart;
    }

    public function getDateEnd(): ?Carbon
    {
        return $this->dateEnd;
    }
}
