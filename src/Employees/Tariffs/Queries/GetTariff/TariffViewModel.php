<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Queries\GetTariff;

use DateTimeImmutable;
use Src\Core\Constants\DateFormats;
use Src\Core\ValueObjects\ClassifierViewModel;
use Src\Employees\Tariffs\ValueObjects\PriceHourInterval;

final class TariffViewModel
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var float */
    private $priceCfg;

    /** @var DateTimeImmutable */
    private $dateFrom;

    /** @var DateTimeImmutable */
    private $dateTo;

    /** @var int */
    private $defaultPrice;

    /** @var array<PriceHourInterval> */
    private $hours;

    /** @var ClassifierViewModel */
    private $town;

    /** @var ClassifierViewModel */
    private $role;

    /** @var ClassifierViewModel|null */
    private $point;

    /**
     * @param int $id
     * @param string $name
     * @param float $priceCfg
     * @param DateTimeImmutable $dateFrom
     * @param DateTimeImmutable $dateTo
     * @param int $defaultPrice
     * @param PriceHourInterval[] $hours
     * @param ClassifierViewModel $town
     * @param ClassifierViewModel $role
     * @param ClassifierViewModel|null $point
     */
    public function __construct(
        int $id,
        string $name,
        float $priceCfg,
        DateTimeImmutable $dateFrom,
        DateTimeImmutable $dateTo,
        int $defaultPrice,
        array $hours,
        ClassifierViewModel $town,
        ClassifierViewModel $role,
        ?ClassifierViewModel $point
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->priceCfg = $priceCfg;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->defaultPrice = $defaultPrice;
        $this->hours = $hours;
        $this->town = $town;
        $this->role = $role;
        $this->point = $point;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->priceCfg,
            'date_from' => $this->dateFrom->format(DateFormats::SYSTEM_DATE),
            'date_to' => $this->dateTo->format(DateFormats::SYSTEM_DATE),
            'default_price' => $this->defaultPrice,
            'price_cfg' => $this->priceCfg,
            'hours' => array_map(function (PriceHourInterval $hour) {
                return $hour->toArray();
            }, $this->hours),
            'town' => $this->town->toArray(),
            'role' => $this->role->toArray(),
            'point' => optional($this->point)->toArray(),
        ];
    }
}
