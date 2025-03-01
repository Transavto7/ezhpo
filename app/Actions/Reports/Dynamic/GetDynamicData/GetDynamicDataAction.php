<?php

namespace App\Actions\Reports\Dynamic\GetDynamicData;

class GetDynamicDataAction
{
    private $journal;
    private $companyId;
    private $pointId;
    private $townId;
    private $orderBy;

    public function __construct($journal, $pointId, $townId, $orderBy, $companyId)
    {
        $this->journal = $journal;
        $this->companyId = $companyId;
        $this->pointId = $pointId;
        $this->townId = $townId;
        $this->orderBy = $orderBy;
    }

    public function getCompanyId()
    {
        return $this->companyId;
    }

    public function getPointId()
    {
        return $this->pointId;
    }

    public function getTownId()
    {
        return $this->townId;
    }

    public function getJournal()
    {
        return $this->journal;
    }

    public function getOrderBy()
    {
        return $this->orderBy;
    }
}
