<?php

namespace App\Actions\Reports\OneC\Create;

class ReportPayload
{
    /**
     * @var \DateTimeImmutable
     */
    private $dateTo;

    /**
     * @var \DateTimeImmutable
     */
    private $dateFrom;

    /**
     * @var string
     */
    private $companyId;

    /**
     * @param \DateTimeImmutable $dateTo
     * @param \DateTimeImmutable $dateFrom
     * @param string $companyId
     */
    public function __construct(\DateTimeImmutable $dateTo, \DateTimeImmutable $dateFrom, string $companyId)
    {
        $this->dateTo = $dateTo;
        $this->dateFrom = $dateFrom;
        $this->companyId = $companyId;
    }

    public function getDateTo(): \DateTimeImmutable
    {
        return $this->dateTo;
    }

    public function getDateFrom(): \DateTimeImmutable
    {
        return $this->dateFrom;
    }

    public function getCompanyId(): string
    {
        return $this->companyId;
    }

}
