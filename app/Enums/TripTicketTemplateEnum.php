<?php

namespace App\Enums;

class TripTicketTemplateEnum
{
    const S4 = '4s';

    /** @var string */
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        switch ($value) {
            case self::S4:
                return self::s4();
            default:
                throw new \DomainException('Unknown trip ticket template type: ' . $value);
        }
    }

    public static function s4(): self
    {
        return new self(self::S4);
    }

    public static function labels(): array
    {
        return [
            self::S4 => '4-С',
        ];
    }

    public static function getLabel(string $value): string
    {
        return self::labels()[$value];
    }
}
