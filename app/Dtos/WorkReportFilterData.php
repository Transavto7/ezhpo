<?php

namespace App\Dtos;

use App\Dtos\Contracts\MutableDTO;
use App\Values\WorkReport\FilterDateValue;
use Carbon\CarbonPeriod;

class WorkReportFilterData extends MutableDTO
{
    /**
     * @var object|null
     */
    public ?FilterDateValue $dateFrom = null;
    /**
     * @var object|null
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
}
