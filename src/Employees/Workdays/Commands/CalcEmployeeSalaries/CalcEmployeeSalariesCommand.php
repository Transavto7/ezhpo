<?php

namespace Src\Employees\Workdays\Commands\CalcEmployeeSalaries;

final class CalcEmployeeSalariesCommand
{
    /**
     * @var int Год
     */
    private $year;

    /**
     * @var int Месяц
     */
    private $month;

    /**
     * @var int|null id города
     */
    private $town = null;

    /**
     * @var int|null id роли
     */
    private $role = null;

    /**
     * @var array id пунктов
     */
    private $pointList = [];

    /**
     * @var array id сотрудников
     */
    private $employeeList = [];

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function setMonth(int $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getTown(): ?int
    {
        return $this->town;
    }

    public function setTown(?int $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function getRole(): ?int
    {
        return $this->role;
    }

    public function setRole(?int $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getPointList(): array
    {
        return $this->pointList;
    }

    public function setPointList(array $pointList): self
    {
        $this->pointList = $pointList;

        return $this;
    }

    public function getEmployeeList(): array
    {
        return $this->employeeList;
    }

    public function setEmployeeList(array $employeeList): self
    {
        $this->employeeList = $employeeList;

        return $this;
    }
}
