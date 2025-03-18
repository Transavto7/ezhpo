<?php

namespace Src\Employees\Workdays\Commands\CalcEmployeeSalaries;

use DateTime;
use Exception;

final class CalcEmployeeSalary implements \JsonSerializable
{
    /**
     * @var CalcEmployeeSalarySettingList
     */
    private static $calcEmployeeSalarySettingList;

    private $employeeId;
    private $employeeName = null;

    private $mapForRole = [];

    public function __construct(int $employeeId)
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @param CalcEmployeeSalarySettingList $calcEmployeeSalarySettingList
     */
    public static function setCalcEmployeeSalarySettingList(CalcEmployeeSalarySettingList $calcEmployeeSalarySettingList): void
    {
        self::$calcEmployeeSalarySettingList = $calcEmployeeSalarySettingList;
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
        if (!$pointId && !$townId) {
            throw new Exception('Для расчета зарплаты необходимо указать либо точку, либо город');
        }

        $dateFrom = new DateTime($openDatetime);
        $dateFromRound = new DateTime($openDatetimeRound);
        $dateTo = new DateTime($closeDatetime);
        $dateToRound = new DateTime($closeDatetimeRound);

        if ($dateFrom->diff($dateTo)->invert || $dateFromRound->diff($dateToRound)->invert) {
            throw new Exception('Дата открытия не может быть больше даты закрытия');
        }

        $setting = self::$calcEmployeeSalarySettingList;

        if (empty($this->mapForRole[$roleId])) {
            $this->mapForRole[$roleId] = [
                'minute' => 0,
                'salary' => 0
            ];
        }

        for (; $dateFromRound->diff($dateToRound)->h > 0; $dateFromRound->add($setting->getIntervalOneHour())) {
            $price = $setting->getPrice($townId, $pointId, $roleId, $dateFromRound);

            $minutes = 60;

            $closeInterval = null;

            // Если это не полный час (N минут от начала смены)
            $openInterval = $dateFromRound->diff($dateFrom);
            if (!$openInterval->h && !$openInterval->invert) {
                $minutes = 60 - $openInterval->i;
            } else {
                // Если это не полный час (N минут от конца смены)
                $closeInterval = $dateFromRound->diff($dateTo);
                if (!$closeInterval->h && !$closeInterval->invert) {
                    $minutes = $closeInterval->i;
                }
            }

            $hourPrice = $isHoliday ? ($price->getPrice() + $price->getPriceCfg()) : $price->getPrice();

            $this->mapForRole[$roleId]['salary'] += $minutes === 60
                ? $hourPrice : $hourPrice / 60 * $minutes;

            $this->mapForRole[$roleId]['minute'] += $minutes;
        }
    }

    public function getSalary(): int
    {
        $countRoles = count($this->mapForRole);
        if (!$countRoles) {
            return 0;
        }
        if ($countRoles === 1) {
            return $this->mapForRole[array_key_first($this->mapForRole)]['salary'];
        }

        return max(array_column($this->mapForRole, 'salary'));
    }

    public function getMinute(): int
    {
        if (count($this->mapForRole)) {
            return $this->mapForRole[array_key_first($this->mapForRole)]['minute'];
        }

        return 0;
    }

    public function toArray(): array
    {
        return [
            'salary' => $this->getSalary(),
            'minute' => $this->getMinute(),
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
     * @return CalcEmployeeSalary
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
