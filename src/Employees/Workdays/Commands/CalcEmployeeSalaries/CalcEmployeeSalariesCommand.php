<?php

namespace Src\Employees\Workdays\Commands\CalcEmployeeSalaries;

use DateTime;

final class CalcEmployeeSalariesCommand
{
    private $dateFrom = null;
    private $dateTo = null;

    /**
     * @return DateTime|null
     */
    public function getDateFrom(): ?DateTime
    {
        return $this->dateFrom;
    }

    /**
     * @param DateTime|string|null $dateFrom
     * @return CalcEmployeeSalariesCommand
     * @throws \DateMalformedStringException
     */
    public function setDateFrom($dateFrom): self
    {
        if (is_string($dateFrom)) {
            $this->dateFrom = new DateTime($dateFrom);
        } elseif ($dateFrom instanceof DateTime) {
            $this->dateFrom = $dateFrom;
        } else {
            $this->dateFrom = null;
        }

        return $this;
    }

    /**
     * @return DateTime:null
     */
    public function getDateTo(): ?DateTime
    {
        return $this->dateTo;
    }

    /**
     * @param DateTime|string|null $dateTo
     * @return CalcEmployeeSalariesCommand
     * @throws \DateMalformedStringException
     */
    public function setDateTo($dateTo): self
    {
        if (is_string($dateTo)) {
            $this->dateTo = new DateTime($dateTo);
        } elseif ($dateTo instanceof DateTime) {
            $this->dateTo = $dateTo;
        } else {
            $this->dateTo = null;
        }

        return $this;
    }
}
