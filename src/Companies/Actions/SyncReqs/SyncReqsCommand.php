<?php

namespace Src\Companies\Actions\SyncReqs;

class SyncReqsCommand
{
    /** @var int */
    private $companyId;

    /**
     * @param int $companyId
     */
    public function __construct(int $companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * @return int
     */
    public function getCompanyId(): int
    {
        return $this->companyId;
    }
}
