<?php

namespace App\ValueObjects;

use App\Driver;

class PressureLimits
{
    /**
     * @var float
     */
    private $maxSystolic;

    /**
     * @var float
     */
    private $maxDiastolic;

    private function __construct(float $maxSystolic, float $maxDiastolic)
    {
        $this->maxSystolic = $maxSystolic;
        $this->maxDiastolic = $maxDiastolic;
    }

    public static function create(Driver $driver = null): self
    {
        $maxSystolic = 150;
        $maxDiastolic = 100;

        if ($driver) {
            $maxSystolic = $driver->getPressureSystolic();
            $maxDiastolic = $driver->getPressureDiastolic();
        }

        return new self($maxSystolic, $maxDiastolic);
    }

    /**
     * @return float
     */
    public function getMaxSystolic(): float
    {
        return $this->maxSystolic;
    }

    /**
     * @return float
     */
    public function getMaxDiastolic(): float
    {
        return $this->maxDiastolic;
    }
}
