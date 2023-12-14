<?php

namespace App\ValueObjects;

use App\Driver;

class PulseLimits
{
    /**
     * @var int
     */
    private $minPulse;

    /**
     * @var int
     */
    private $maxPulse;

    /**
     * @param int $minPulse
     * @param int $maxPulse
     */
    public function __construct(int $minPulse, int $maxPulse)
    {
        $this->minPulse = $minPulse;
        $this->maxPulse = $maxPulse;
    }


    public static function create(Driver $driver = null): self
    {
        $minPulse = PHP_INT_MIN;
        $maxPulse = PHP_INT_MAX;

        if ($driver) {
            $minPulse = $driver->getPulseLower();
            $maxPulse = $driver->getPulseUpper();
        }

        return new self($minPulse, $maxPulse);
    }

    public function getMaxPulse(): int
    {
        return $this->maxPulse;
    }

    public function getMinPulse(): int
    {
        return $this->minPulse;
    }
}
