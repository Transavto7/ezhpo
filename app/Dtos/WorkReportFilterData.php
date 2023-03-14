<?php

namespace App\Dtos;

use App\Dtos\Contracts\MutableDTO;
use App\Values\WorkReport\FilterDateValue;

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
     * @var int|string
     */
    public int $perPage = 150;

    public function __construct(array $parameters = [])
    {
        parent::__construct($parameters);
    }
}