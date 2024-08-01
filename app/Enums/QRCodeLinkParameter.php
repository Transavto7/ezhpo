<?php

namespace App\Enums;

final class QRCodeLinkParameter
{
    const DRIVER_ID = 'driverId';
    const CAR_ID = 'carId';

    /** @var string */
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        switch ($value) {
            case self::DRIVER_ID:
                return self::driverId();
            case self::CAR_ID:
                return self::carId();
            default:
                throw new \DomainException('Unknown QR code parameter: ' . $value);
        }
    }

    public static function driverId(): self
    {
        return new self(self::DRIVER_ID);
    }

    public static function carId(): self
    {
        return new self(self::CAR_ID);
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public static function cases(): array
    {
        return [
            self::DRIVER_ID,
            self::CAR_ID,
        ];
    }
}
