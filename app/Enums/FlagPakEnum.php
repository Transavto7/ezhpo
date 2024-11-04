<?php

namespace App\Enums;

class FlagPakEnum
{
    const INTERNAL = 'internal';

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
            case self::INTERNAL:
                return self::internal();
            default:
                throw new \DomainException('Unknown flag pak: ' . $value);
        }
    }

    public static function internal(): self
    {
        return new self(self::INTERNAL);
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public static function cases(): array
    {
        return [
            self::INTERNAL,
        ];
    }

    public static function getLabel(string $value): string
    {
        switch ($value) {
            case self::INTERNAL:
                return 'Очный';
            default:
                return $value;
        }
    }
}
