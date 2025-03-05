<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Actions\UpdateTariff;

use DateTimeImmutable;
use Src\Employees\Tariffs\ValueObjects\PriceHourInterval;

final class UpdateTariffAction
{
    /** @var int */
    private $tariffId;

    /** @var string */
    private $name;

    /** @var int */
    private $townId;

    /** @var int|null */
    private $pointId;

    /** @var int */
    private $roleId;

    /** @var float */
    private $priceCfg;

    /** @var DateTimeImmutable */
    private $dateFrom;

    /** @var DateTimeImmutable */
    private $dateTo;

    /** @var int */
    private $defaultPrice;

    /** @var array<PriceHourInterval> */
    private $customHours;

    /**
     * @param int $tariffId
     * @param string $name
     * @param int $townId
     * @param int|null $pointId
     * @param int $roleId
     * @param float $priceCfg
     * @param DateTimeImmutable $dateFrom
     * @param DateTimeImmutable $dateTo
     * @param int $defaultPrice
     * @param PriceHourInterval[] $customHours
     */
    public function __construct(
        int $tariffId,
        string $name,
        int $townId,
        ?int $pointId,
        int $roleId,
        float $priceCfg,
        DateTimeImmutable $dateFrom,
        DateTimeImmutable $dateTo,
        int $defaultPrice,
        array $customHours
    ) {
        $this->tariffId = $tariffId;
        $this->name = $name;
        $this->townId = $townId;
        $this->pointId = $pointId;
        $this->roleId = $roleId;
        $this->priceCfg = $priceCfg;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->defaultPrice = $defaultPrice;
        $this->customHours = $customHours;
    }

    public function getTariffId(): int
    {
        return $this->tariffId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTownId(): int
    {
        return $this->townId;
    }

    public function getPointId(): ?int
    {
        return $this->pointId;
    }

    public function getRoleId(): int
    {
        return $this->roleId;
    }

    public function getPriceCfg(): float
    {
        return $this->priceCfg;
    }

    public function getDateFrom(): DateTimeImmutable
    {
        return $this->dateFrom;
    }

    public function getDateTo(): DateTimeImmutable
    {
        return $this->dateTo;
    }

    public function getDefaultPrice(): int
    {
        return $this->defaultPrice;
    }

    public function getCustomHours(): array
    {
        return $this->customHours;
    }
}
