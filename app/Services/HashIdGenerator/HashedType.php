<?php
declare(strict_types=1);

namespace App\Services\HashIdGenerator;

final class HashedType
{
    private const USER = 'user';
    private const CAR = 'car';
    private const DRIVER = 'driver';
    private const COMPANY = 'company';
    private const PRODUCT = 'product';
    private const DEFAULT = 'default';

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

    public static function default(): self
    {
        return new self(self::DEFAULT);
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
