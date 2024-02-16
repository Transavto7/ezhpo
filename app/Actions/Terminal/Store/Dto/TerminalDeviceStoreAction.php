<?php

namespace App\Actions\Terminal\Store\Dto;

final class TerminalDeviceStoreAction
{
    /**
     * @var int
     */
    private $userId;
    /**
     * @var string
     */
    private $deviceName;
    /**
     * @var string
     */
    private $deviceSerialNumber;

    /**
     * @param int $userId
     * @param string $deviceName
     * @param string $deviceSerialNumber
     */
    public function __construct(int $userId, string $deviceName, string $deviceSerialNumber)
    {
        $this->userId = $userId;
        $this->deviceName = $deviceName;
        $this->deviceSerialNumber = $deviceSerialNumber;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getDeviceName(): string
    {
        return $this->deviceName;
    }

    public function getDeviceSerialNumber(): string
    {
        return $this->deviceSerialNumber;
    }


}
