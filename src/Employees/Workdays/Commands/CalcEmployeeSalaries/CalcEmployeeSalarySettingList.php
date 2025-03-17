<?php

namespace Src\Employees\Workdays\Commands\CalcEmployeeSalaries;

use DateInterval;
use DateTime;

class CalcEmployeeSalarySettingList
{
    public $mapForHours;
    private $intervalOneDay;

    public function __construct()
    {
        $this->mapForHours = [];
        $this->intervalOneDay = new DateInterval('P1D');
    }

    public function addRow(?int $townId, ?int $pointId, int $roleId, int $priceCfg, string $dateFrom, string $dateTo, int $hour, int $price)
    {
        if (is_null($townId)) {
            $townId = 0;
        }
        if (is_null($pointId)) {
            $pointId = 0;
        }

        $dateFrom = new DateTime($dateFrom);
        $dateTo = new DateTime($dateTo);

        // Защита от того, что время конца меньше времени начала
        if ($dateFrom->diff($dateTo)->invert) {
            return;
        }

        // Прибавляем, потому что дата включительно
        $dateTo->add($this->intervalOneDay);

        $price = new CalcEmployeeSalaryPrice($price, $priceCfg);
        for (; $dateFrom->diff($dateTo)->days > 0; $dateFrom->add($this->intervalOneDay)) {
            $this->mapForHours[$townId][$pointId][$roleId][(int)$dateFrom->format('Ymd')][$hour] = $price;
        }
    }
}
