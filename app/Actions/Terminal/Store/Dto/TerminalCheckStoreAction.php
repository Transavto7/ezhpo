<?php

namespace App\Actions\Terminal\Store\Dto;

use Carbon\Carbon;

final class TerminalCheckStoreAction
{
    /**
     * @var int
     */
    private $userId;
    /**
     * @var string
     */
    private $serialNumber;
    /**
     * @var Carbon
     */
    private $dateCheck;

    /**
     * @param int $userId
     * @param string $serialNumber
     * @param Carbon $dateCheck
     */
    public function __construct(
        int $userId,
        string $serialNumber,
        Carbon $dateCheck
    ) {
        $this->userId = $userId;
        $this->serialNumber = $serialNumber;
        $this->dateCheck = $dateCheck;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getSerialNumber(): string
    {
        return $this->serialNumber;
    }

    public function getDateCheck(): Carbon
    {
        return $this->dateCheck;
    }
}
