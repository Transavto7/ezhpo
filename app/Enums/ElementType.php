<?php
declare(strict_types=1);

namespace App\Enums;

final class ElementType
{
    const USER = 'user';
    const CAR = 'car';
    const DRIVER = 'driver';
    const COMPANY = 'company';
    const PRODUCT = 'product';

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
            case self::USER:
                return self::user();
            case self::CAR:
                return self::car();
            case self::DRIVER:
                return self::driver();
            case self::COMPANY:
                return self::company();
            case self::PRODUCT:
                return self::product();
            default:
                throw new \DomainException('Unknown element type: ' . $value);
        }
    }

    public static function user(): self
    {
        return new self(self::USER);
    }

    public static function car(): self
    {
        return new self(self::CAR);
    }

    public static function driver(): self
    {
        return new self(self::DRIVER);
    }

    public static function company(): self
    {
        return new self(self::COMPANY);
    }

    public static function product(): self
    {
        return new self(self::PRODUCT);
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public static function cases(): array
    {
        return [
           self::USER,
           self::CAR,
           self::DRIVER,
           self::COMPANY,
           self::PRODUCT
        ];
    }
}
