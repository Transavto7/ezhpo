<?php

namespace App\Actions\Element\Metric;

use Carbon\Carbon;

class GenerateMetricAction
{
    /**
     * @var Carbon
     */
    protected $startDate;

    /**
     * @var Carbon
     */
    protected $endDate;

    /**
     * @param Carbon $startDate
     * @param Carbon $endDate
     */
    public function __construct(Carbon $startDate, Carbon $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return Carbon
     */
    public function getStartDate(): Carbon
    {
        return $this->startDate;
    }

    /**
     * @return Carbon
     */
    public function getEndDate(): Carbon
    {
        return $this->endDate;
    }
}
