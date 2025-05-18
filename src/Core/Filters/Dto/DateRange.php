<?php

namespace Src\Core\Filters\Dto;

use Carbon\Carbon;

final class DateRange
{
    /**
     * @var Carbon|null
     */
    protected $start;
    /**
     * @var Carbon|null
     */
    protected $end;

    /**
     * @param Carbon|null $start
     * @param Carbon|null $end
     */
    public function __construct(?Carbon $start, ?Carbon $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function getStart(): ?Carbon
    {
        return $this->start;
    }

    public function getEnd(): ?Carbon
    {
        return $this->end;
    }
}