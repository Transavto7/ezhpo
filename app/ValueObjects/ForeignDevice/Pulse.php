<?php

namespace App\ValueObjects\ForeignDevice;

class Pulse implements ForeignDeviceInterface
{
    private $pulse;

    /**
     * @param int $pulse
     */
    public function __construct(int $pulse)
    {
        $this->pulse = $pulse;
    }

    public static function random(): Pulse
    {
        return new self(mt_rand(60,80));
    }

    public function isAdmitted(?ForeignDeviceLimitInterface $pulseLimits): bool
    {
        if (!($pulseLimits instanceof PulseLimit)) {
            throw new \Exception('Неверный тип данных для pulseLimits');
        }

        if ($this->pulse >= $pulseLimits->getMaxPulse()) {
            return false;
        }

        if ($this->pulse <= $pulseLimits->getMinPulse()) {
            return false;
        }

        return true;
    }

    public function getPulse(): int
    {
        return $this->pulse;
    }
}
