<?php

namespace App\Enums;

class LogisticsMethodEnum
{
    const URBAN = 'urban';

    const SUBURBAN = 'suburban';

    const LONG_DISTANCE = 'long_distance';

    const INTERNATIONAL = 'international';

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
            case self::URBAN:
                return self::urban();
            case self::SUBURBAN:
                return self::suburban();
            case self::LONG_DISTANCE:
                return self::longDistance();
            case self::INTERNATIONAL:
                return self::international();
            default:
                throw new \DomainException('Unknown logistics method: ' . $value);
        }
    }

    public static function urban(): self
    {
        return new self(self::URBAN);
    }

    public static function suburban(): self
    {
        return new self(self::SUBURBAN);
    }

    public static function longDistance(): self
    {
        return new self(self::LONG_DISTANCE);
    }

    public static function international(): self
    {
        return new self(self::INTERNATIONAL);
    }

    public static function labels(): array
    {
        return [
            self::URBAN => 'Городское',
            self::SUBURBAN => 'Пригородное',
            self::LONG_DISTANCE => 'Междугородное',
            self::INTERNATIONAL => 'Международное',
        ];
    }

    public static function getLabel(string $value): string
    {
        return self::labels()[$value];
    }
}
