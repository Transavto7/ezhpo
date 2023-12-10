<?php

namespace App\ValueObjects;

use App\Driver;

class Tonometer
{
    private $systolic;

    private $diastolic;

    /**
     * @param float $systolic
     * @param float $diastolic
     */
    public function __construct(float $systolic, float $diastolic)
    {
        $this->systolic = $systolic;
        $this->diastolic = $diastolic;
    }

    public function __toString(): string
    {
        return $this->systolic . '/' . $this->diastolic;
    }

    public static function random(Driver $driver = null): self
    {
        $systolic = rand(100, 139);
        $diastolic = rand(60, 89);

        if ($driver) {
            if ($systolic >= intval($driver->getPressureSystolic())) {
                $systolic = intval($driver->getPressureSystolic()) - rand(1, 10);
            }

            if ($diastolic >= intval($driver->getPressureDiastolic())) {
                $diastolic = intval($driver->getPressureDiastolic()) - rand(1, 10);
            }
        }

        return new self($systolic, $diastolic);
    }

    public static function fromString(string $tonometer): self
    {
        $values = explode('/', $tonometer);

        return new self($values[0], $values[1]);
    }

    public static function create(float $systolic, float $diastolic): self
    {
        return new self($systolic, $diastolic);
    }

    public function isAdmitted(PressureLimits $pressureLimits): bool
    {
        if ($this->systolic > $pressureLimits->getMaxSystolic()) {
            return false;
        }

        if ($this->diastolic > $pressureLimits->getMaxDiastolic()) {
            return false;
        }

        return true;
    }
}
