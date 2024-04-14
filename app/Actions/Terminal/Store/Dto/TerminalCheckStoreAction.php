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
     * @var Carbon
     */
    private $dateServiceStart;
    /**
     * @var Carbon
     */
    private $dateServiceEnd;
    /**
     * @var int
     */
    private $failuresCount;

    /**
     * @param int $userId
     * @param string $serialNumber
     * @param Carbon $dateCheck
     * @param Carbon $dateServiceStart
     * @param Carbon $dateServiceEnd
     * @param int $failuresCount
     */
    public function __construct(
        int $userId,
        string $serialNumber,
        Carbon $dateCheck,
        Carbon $dateServiceStart,
        Carbon $dateServiceEnd,
        int $failuresCount
    ) {
        $this->userId = $userId;
        $this->serialNumber = $serialNumber;
        $this->dateCheck = $dateCheck;
        $this->dateServiceStart = $dateServiceStart;
        $this->dateServiceEnd = $dateServiceEnd;
        $this->failuresCount = $failuresCount;
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

    /**
     * @return Carbon
     */
    public function getDateServiceStart(): Carbon
    {
        return $this->dateServiceStart;
    }

    /**
     * @return Carbon
     */
    public function getDateServiceEnd(): Carbon
    {
        return $this->dateServiceEnd;
    }

    /**
     * @return int
     */
    public function getFailuresCount(): int
    {
        return $this->failuresCount;
    }
}
