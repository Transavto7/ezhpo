<?php

namespace App\Dtos;

use App\Dtos\Contracts\MutableDTO;
use App\Values\WorkReport\FilterDateValue;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class WorkReportFilterData extends MutableDTO
{
    /**
     * @var FilterDateValue|null
     */
    public ?FilterDateValue $dateFrom = null;
    /**
     * @var FilterDateValue|null
     */
    public ?FilterDateValue $dateTo = null;
    /**
     * @var int|null
     */
    public ?int $userId = null;
    /**
     * @var int|null
     */
    public ?int $pvId = null;
    /**
     * @var int|null
     */
    public ?int $perPage = 150;

    /**
     * @var string|null
     */
    public ?string $orderBy = null;

    public function getDatesPeriod(): CarbonPeriod
    {
        return CarbonPeriod::create(
            $this->dateFrom->getValue(),
            $this->dateTo->getValue());
    }
}
