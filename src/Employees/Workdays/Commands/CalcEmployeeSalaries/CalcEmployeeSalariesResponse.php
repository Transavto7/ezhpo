<?php

namespace Src\Employees\Workdays\Commands\CalcEmployeeSalaries;

use DateTime;

final class CalcEmployeeSalariesResponse implements \JsonSerializable
{
    private $employeeId;
    private $employeeName = null;


    public function __construct(int $employeeId)
    {
        $this->employeeId = $employeeId;
    }

    public function addWorkday(
        string $openDatetimeRound,
        string $closeDatetimeRound,
        string $openDatetime,
        string $closeDatetime,
        int    $roleId,
        ?int   $pointId,
        ?int   $townId,
        bool   $isHoliday
    )
    {

    }

    public function getSalary(): int
    {
        return 0;
    }

    public function getMinutes(): int
    {
        return 0;
    }

    public function toArray(): array
    {
        return [
            'salary' => $this->getSalary(),
            'minutes' => $this->getMinutes(),
            'employeeId' => $this->getEmployeeId(),
            'employeeName' => $this->getEmployeeName()
        ];
    }

    /**
     * @return string|null
     */
    public function getEmployeeName(): ?string
    {
        return $this->employeeName;
    }

    /**
     * @param string|null $employeeName
     * @return CalcEmployeeSalariesResponse
     */
    public function setEmployeeName(?string $employeeName): self
    {
        $this->employeeName = $employeeName;
        return $this;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
