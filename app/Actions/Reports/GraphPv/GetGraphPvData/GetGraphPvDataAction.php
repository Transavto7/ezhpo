<?php

namespace App\Actions\Reports\GraphPv\GetGraphPvData;

use Carbon\Carbon;

class GetGraphPvDataAction
{
    /**
     * @var
     */
    private $pvId;

    /**
     * @var string
     */
    private $formType;

    /**
     * @var Carbon
     */
    private $dateFrom;

    /**
     * @var Carbon
     */
    private $dateTo;

    /**
     * @param array $pvId
     * @param string $formType
     * @param Carbon $dateFrom
     * @param Carbon $dateTo
     */
    public function __construct(array $pvId, string $formType, Carbon $dateFrom, Carbon $dateTo)
    {
        $this->pvId = $pvId;
        $this->formType = $formType;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    /**
     * @return array
     */
    public function getPvId(): array
    {
        return $this->pvId;
    }

    /**
     * @return string
     */
    public function getFormType(): string
    {
        return $this->formType;
    }

    /**
     * @return Carbon
     */
    public function getDateFrom(): Carbon
    {
        return $this->dateFrom;
    }

    /**
     * @return Carbon
     */
    public function getDateTo(): Carbon
    {
        return $this->dateTo;
    }
}
