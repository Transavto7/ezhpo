<?php

namespace App\ValueObjects\ForeignDevice;

class Temperature implements ForeignDeviceInterface
{
    /**
     * @var float
     */
    private $temperature;

    public function __construct(float $temperature)
    {
        $this->temperature = $temperature;
    }

    public static function random(): self
    {
        return new self(mt_rand(359, 367)/10);
    }

    public function isAdmitted(?ForeignDeviceLimitInterface $deviceLimits = null): bool
    {
        if ($this->temperature >= 37) {
            return false;
        }

        return true;
    }

    public function getTemperature(): float
    {
        return $this->temperature;
    }
}
