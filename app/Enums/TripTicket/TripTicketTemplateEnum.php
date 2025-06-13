<?php

namespace App\Enums\TripTicket;

class TripTicketTemplateEnum
{
    const S4 = '4s';

    const _3 = '3';
    const _4P = '4p';
    const PG1 = 'pg1';
    const _6C = '6c';
    const _3C = '3c';
    const _4O = '4o';
    const ECM2 = 'ecm2';

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
            case self::_3:
                return self::_3();
            case self::_4P:
                return self::_4p();
            case self::PG1:
                return self::pg1();
            case self::_6C:
                return self::_6c();
            case self::_3C:
                return self::_3c();
            case self::_4O:
                return self::_4o();
            case self::ECM2:
                return self::ecm2();
            default:
                throw new \DomainException('Unknown trip ticket template type: ' . $value);
        }
    }

    public static function s4(): self
    {
        return new self(self::S4);
    }

    public static function _3(): self
    {
        return new self(self::_3);
    }

    public static function _4p(): self
    {
        return new self(self::_4P);
    }

    public static function pg1(): self
    {
        return new self(self::PG1);
    }

    public static function _6c(): self
    {
        return new self(self::_6C);
    }

    public static function _3c(): self
    {
        return new self(self::_3C);
    }

    public static function _4o(): self
    {
        return new self(self::_4O);
    }

    public static function ecm2(): self
    {
        return new self(self::ECM2);
    }

    public static function labels(): array
    {
        return [
            self::S4 => '4-С',
            self::_3 => '3',
            self::_4P => '4-П',
            self::PG1 => 'ПГ-1',
            self::_6C => '6-С',
            self::_3C => '3-спец.',
            self::_4O => '4-О',
            self::ECM2 => 'ЭСМ-2',
        ];
    }

    public static function getLabel(string $value): string
    {
        return self::labels()[$value];
    }
}
