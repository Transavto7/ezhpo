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
        function mt_rand_float($min, $max, $countZero = '0') {
            $countZero = +('1'.$countZero);
            $min = floor($min * $countZero);
            $max = floor($max * $countZero);
            return mt_rand($min, $max) / $countZero;
        }

        return new self(mt_rand_float(35.9,36.7));
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
