<?php

namespace Src\Employees\Workdays\Commands\CalcEmployeeSalaries;

use Complex\Exception;
use DateInterval;
use DateTime;

class CalcEmployeeSalarySettingList
{
    public $mapForHours;
    private $intervalOneDay;
    private $intervalOneHour;

    public function __construct()
    {
        $this->mapForHours = [];
        $this->intervalOneDay = new DateInterval('P1D');
        $this->intervalOneHour = new DateInterval('PT1H');
    }

    public function addRow(?int $townId, ?int $pointId, int $roleId, int $priceCfg, string $dateFrom, string $dateTo, int $hour, int $price)
    {
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

    public function getPrice(?int $townId, ?int $pointId, int $roleId, DateTime $dateTime): CalcEmployeeSalaryPrice
    {
        if ($price = $this->mapForHours[$townId][$pointId][$roleId][(int)$dateTime->format('Ymd')][(int)$dateTime->format('H')] ?? null) {
            return $price;
        }

        // Ничего не найдено, но задан и поинт и город, пытаемся найти ТОЛЬКО по городу или ТОЛЬКО по поинту
        if ($townId && $pointId) {
            try {
                return $this->getPrice(null, $pointId, $roleId, $dateTime);
            } catch (\Exception $e) {
            }

            try {
                return $this->getPrice($townId, null, $roleId, $dateTime);
            } catch (\Exception $e) {
            }
        }

        throw new Exception(
            sprintf('Для указанной даты и времени не задан тариф (Дата: %s`, Время: %s, townId: %s, pointId: %s, roleId: %s)',
                $dateTime->format('Y-m-d'),
                $dateTime->format('H'),
                is_null($townId) ? -1 : $townId,
                is_null($pointId) ? -1: $pointId,
                $roleId
            )
        );
    }

    public function getIntervalOneDay(): DateInterval
    {
        return $this->intervalOneDay;
    }

    public function getIntervalOneHour(): DateInterval
    {
        return $this->intervalOneHour;
    }
}
