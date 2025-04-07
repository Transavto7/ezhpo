<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\ValueObjects;

final class PriceHourInterval
{
    /** @var int */
    private $start;

    /** @var int */
    private $end;

    /** @var int */
    private $price;

    /**
     * @param int $start
     * @param int $end
     * @param int $price
     */
    public function __construct(int $start, int $end, int $price)
    {
        $this->start = $start;
        $this->end = $end;
        $this->price = $price;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function toArray(): array
    {
        return [
            'start' => $this->start,
            'end' => $this->end,
            'price' => $this->price,
        ];
    }
}
