<?php

namespace App\Enums;

use DomainException;
use ReflectionClass;

final class FormFeedbackEnum
{
    const POSITIVE = 'positive';
    const NEGATIVE = 'negative';

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
            case self::POSITIVE:
                return self::positive();
            case self::NEGATIVE:
                return self::negative();
            default:
                throw new DomainException('Unsupported form feedback value "'.$value.'"');
        }
    }

    public static function positive(): self
    {
        return new self(self::POSITIVE);
    }

    public static function negative(): self
    {
        return new self(self::NEGATIVE);
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
