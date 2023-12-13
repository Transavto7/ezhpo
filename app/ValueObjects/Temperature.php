<?php

namespace App\ValueObjects;

class Temperature
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
        return new self(mt_rand(35.9, 36.7));
    }

    public function isAdmitted(): bool
    {
        if ($this->temperature >= 38) {
            return false;
        }

        return true;
    }

    public function getTemperature(): float
    {
        return $this->temperature;
    }
}
