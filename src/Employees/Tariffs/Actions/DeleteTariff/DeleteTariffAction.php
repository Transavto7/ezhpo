<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Actions\DeleteTariff;

final class DeleteTariffAction
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
