<?php

namespace App\ValueObjects\ForeignDevice;

use App\Driver;

class Tonometer implements ForeignDeviceInterface
{
    public const MAX_LEGAL_SYSTOLIC = 139;
    public const MIN_LEGAL_SYSTOLIC = 100;

    public const MAX_LEGAL_DIASTOLIC = 99;
    public const MIN_LEGAL_DIASTOLIC = 60;

    /**
     * @var int
     */
    private $systolic;

    /**
     * @var int
     */
    private $diastolic;

    /**
     * @param int $systolic
     * @param int $diastolic
     */
    public function __construct(int $systolic, int $diastolic)
    {
        $this->systolic = $systolic;
        $this->diastolic = $diastolic;
    }

    public function __toString(): string
    {
        return $this->systolic . '/' . $this->diastolic;
    }

    public static function random(): self
    {
        return new self(self::getNormalizedSystolic(), self::getNormalizedDiastolic());
    }

    public static function randomWithDriver(Driver $driver = null): self
    {
        if (!$driver) {
            return self::random();
        }

        $systolic = self::getNormalizedSystolic();
        $diastolic = self::getNormalizedDiastolic();

        if ($systolic >= intval($driver->getPressureSystolic())) {
            $systolic = intval($driver->getPressureSystolic()) - rand(1, 10);
        }

        if ($diastolic >= intval($driver->getPressureDiastolic())) {
            $diastolic = intval($driver->getPressureDiastolic()) - rand(1, 10);
        }

        return new self($systolic, $diastolic);
    }

    public static function fromString(string $tonometer): self
    {
        $values = explode('/', $tonometer);

        if (empty($values) || count($values) !== 2) {
            throw new \Exception('Неверный формат тонометра');
        }

        return new self($values[0], $values[1]);
    }

    public static function create(int $systolic, int $diastolic): self
    {
        return new self($systolic, $diastolic);
    }

    public function isAdmitted(?ForeignDeviceLimitInterface $pressureLimits): bool
    {
        if (!($pressureLimits instanceof PressureLimit)) {
            throw new \Exception('Неверный тип данных для pressureLimits');
        }

        if ($this->systolic > $pressureLimits->getMaxSystolic()) {
            return false;
        }

        if ($this->diastolic > $pressureLimits->getMaxDiastolic()) {
            return false;
        }

        return true;
    }

    public function needNormalize(PressureLimit $pressureLimits): bool
    {
        if (!$this->isAdmitted($pressureLimits)) {
            return false;
        }

        if ($this->systolic > self::MAX_LEGAL_SYSTOLIC) {
            return true;
        }

        if ($this->diastolic > self::MAX_LEGAL_DIASTOLIC) {
            return true;
        }

        return false;
    }

    public function getNormalized(): self
    {
        $systolic = $this->systolic;
        if ($systolic > self::MAX_LEGAL_SYSTOLIC) {
            $systolic = self::getNormalizedSystolic();
        }

        $diastolic = $this->diastolic;
        if ($diastolic > self::MAX_LEGAL_DIASTOLIC) {
            $diastolic = self::getNormalizedDiastolic();
        }

        return self::create($systolic, $diastolic);
    }

    public static function getNormalizedDiastolic(): int
    {
        return rand(self::MIN_LEGAL_DIASTOLIC, self::MAX_LEGAL_DIASTOLIC);
    }

    public static function getNormalizedSystolic(): int
    {
        return rand(self::MIN_LEGAL_SYSTOLIC, self::MAX_LEGAL_SYSTOLIC);
    }

    public function getSystolic(): int
    {
        return $this->systolic;
    }

    public function getDiastolic(): int
    {
        return $this->diastolic;
    }
}
