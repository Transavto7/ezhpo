<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Queries\GetTariff;

final class GetTariffQuery
{
    /** @var int */
    private $tariffId;

    /**
     * @param int $tariffId
     */
    public function __construct(int $tariffId)
    {
        $this->tariffId = $tariffId;
    }

    public function getTariffId(): int
    {
        return $this->tariffId;
    }
}
