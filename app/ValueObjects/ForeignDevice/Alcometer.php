<?php

namespace App\ValueObjects\ForeignDevice;

class Alcometer implements ForeignDeviceInterface
{
    private $value;
    private $mode;

    public function __construct(float $value, ?int $mode = null)
    {
        $this->value = $value;
        $this->mode = $mode;
    }

    /**
     * @return double|null
     */
    public function getValue(): ?float
    {
        return $this->value;
    }

    /**
     * @return int|null
     */
    public function getMode(): ?int
    {
        return $this->mode;
    }

    public static function random()
    {
        return (new self(0, 0));
    }

    public function isAdmitted(?ForeignDeviceLimitInterface $limits = null): bool
    {
        return $this->getValue() <= 0;
    }
}
