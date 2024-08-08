<?php

namespace App\Actions\Reports\Journal\GetJournalData;

use Illuminate\Support\Carbon;

class GetJournalDataAction
{
    /**
     * @var mixed
     */
    private $companyHashId;

    /**
     * @var Carbon
     */
    private $dateFrom;

    /**
     * @var
     */
    private $dateTo;

    /**
     * @param mixed $companyHashId
     * @param Carbon $dateFrom
     * @param Carbon $dateTo
     */
    public function __construct($companyHashId, Carbon $dateFrom, Carbon $dateTo)
    {
        $this->companyHashId = $companyHashId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    /**
     * @return mixed
     */
    public function getCompanyHashId()
    {
        return $this->companyHashId;
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
    public function getDateTo()
    {
        return $this->dateTo;
    }
}
