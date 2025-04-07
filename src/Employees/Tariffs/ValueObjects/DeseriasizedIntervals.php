<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\ValueObjects;

final class DeseriasizedIntervals
{
    /** @var array<PriceHourInterval> */
    private $intervals;

    /** @var int */
    private $defaultPrice;

    /**
     * @param PriceHourInterval[] $intervals
     * @param int $defaultPrice
     */
    public function __construct(array $intervals, int $defaultPrice)
    {
        $this->intervals = $intervals;
        $this->defaultPrice = $defaultPrice;
    }

    public function getIntervals(): array
    {
        return $this->intervals;
    }

    public function getDefaultPrice(): int
    {
        return $this->defaultPrice;
    }
}
