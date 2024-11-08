<?php

namespace App\Enums;

use LogicException;
use ReflectionClass;

final class AnketaVerificationStatus
{
    const VERIFIED = 'verified';
    const DELETED = 'deleted';

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
            case self::VERIFIED:
                return self::verified();
            case self::DELETED:
                return self::deleted();
            default:
                throw new LogicException('Unknown anketa verification status' . $value);
        }
    }

    public static function verified(): self
    {
        return new self(self::VERIFIED);
    }

    public static function deleted(): self
    {
        return new self(self::DELETED);
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public static function cases(): array
    {
        $ref = new ReflectionClass(self::class);
        return $ref->getConstants();
    }
}
