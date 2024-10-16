<?php

namespace App\Enums;

final class AnketLabelingType
{
    const TECH = 'tech';
    const MEDIC = 'medic';

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
            case self::TECH:
                return self::tech();
            case self::MEDIC:
                return self::medic();
            default:
                throw new \DomainException('Anket labeling type value: ' . $value);
        }
    }

    public static function tech(): self
    {
        return new self(self::TECH);
    }

    public static function medic(): self
    {
        return new self(self::MEDIC);
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public static function cases(): array
    {
        return [
            self::TECH,
            self::MEDIC,
        ];
    }
}
