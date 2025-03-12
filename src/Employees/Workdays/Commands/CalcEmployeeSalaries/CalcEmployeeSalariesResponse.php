<?php

namespace Src\Employees\Workdays\Commands\CalcEmployeeSalaries;

use DateTime;

final class CalcEmployeeSalariesResponse implements \JsonSerializable
{
    private $employeeId;
    private $employeeName = null;

    private $roleMap = [];


    public function __construct(int $employeeId)
    {
        $this->employeeId = $employeeId;
    }

    public function addSalary($openDatetime, $closeDatetime, bool $isHoliday, $range, int $hourPrice, int $cfgPrice, int $roleId)
    {
        if (empty($this->roleMap[$roleId])) {
            $this->roleMap[$roleId] = [
                'minutes' => 0,
                'salaries' => 0
            ];
        }

        $rangeDatetime = new DateTime($range);
        $openDatetime = new DateTime($openDatetime);
        $closeDatetime = new DateTime($closeDatetime);

        $minutes = 60;

        // Если это не полный час (N минут от начала смены)
        $openInterval = $rangeDatetime->diff($openDatetime);
        if (!$openInterval->h && !$openInterval->invert) {
            $minutes = 60 - $openInterval->i;
        } else {
            // Если это не полный час (N минут от конца смены)
            $closeInterval = $rangeDatetime->diff($closeDatetime);
            if (!$closeInterval->h && $closeInterval->invert) {
                $minutes = 60 - $closeInterval->i;
                if ($closeInterval->s) {
                    --$minutes;
                }
            }
        }

        if ($isHoliday) {
            $hourPrice += $cfgPrice;
        }

        $this->roleMap[$roleId]['salaries'] += $minutes === 60
            ? $hourPrice : $hourPrice / 60 * $minutes;

        $this->roleMap[$roleId]['minutes'] += $minutes;
    }

    public function getSalary(): int
    {
        $countRoles = count($this->roleMap);
        if (!$countRoles) {
            return 0;
        }
        if ($countRoles === 1) {
            return $this->roleMap[array_key_first($this->roleMap)]['salaries'];
        }

        return max(array_column($this->roleMap, 'salaries'));
    }

    public function getMinutes(): int
    {
        if (count($this->roleMap)) {
            return $this->roleMap[array_key_first($this->roleMap)]['minutes'];
        }

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
