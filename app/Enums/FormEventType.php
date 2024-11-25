<?php

namespace App\Enums;

use DomainException;
use ReflectionClass;

final class FormEventType
{
    const SET_FEEDBACK = 'set_feedback';

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
            case self::SET_FEEDBACK:
                return self::setFeedback();
            default:
                throw new DomainException('Unsupported form event type: ' . $value);
        }
    }

    public static function setFeedback(): self
    {
        return new self(self::SET_FEEDBACK);
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
