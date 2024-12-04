<?php

namespace App\Enums;

class TransportationTypeEnum
{
    const REGULAR = 'regular';

    const ORDER = 'order';

    const TAXI = 'taxi';

    const CARGO = 'cargo';

    const SELF_NEEDS = 'self_needs';

    const CHILD_TRANSPORTATION = 'child_transportation';

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

    public static function regular(): self
    {
        return new self(self::REGULAR);
    }

    public static function order(): self
    {
        return new self(self::ORDER);
    }

    public static function taxi(): self
    {
        return new self(self::TAXI);
    }

    public static function cargo(): self
    {
        return new self(self::CARGO);
    }

    public static function selfNeeds(): self
    {
        return new self(self::SELF_NEEDS);
    }

    public static function childTransportation(): self
    {
        return new self(self::CHILD_TRANSPORTATION);
    }

    public static function fromString(string $value): self
    {
        switch ($value) {
            case self::REGULAR:
                return self::regular();
            case self::ORDER:
                return self::order();
            case self::TAXI:
                return self::taxi();
            case self::CARGO:
                return self::cargo();
            case self::SELF_NEEDS:
                return self::selfNeeds();
            case self::CHILD_TRANSPORTATION:
                return self::childTransportation();
            default:
                throw new \DomainException('Unknown transportation type: ' . $value);
        }
    }

    public static function labels(): array
    {
        return [
            self::REGULAR => 'Регулярная перевозка пассажиров и багажа',
            self::ORDER => 'Перевозка пассажиров и багажа по заказу',
            self::TAXI => 'Перевозка пассажиров и багажа легковым такси',
            self::CARGO => 'Перевозка грузов',
            self::SELF_NEEDS => 'Перевозка для собственных нужд',
            self::CHILD_TRANSPORTATION => 'Организованная перевозка группы детей',
        ];
    }

    public static function getLabel(string $value): string
    {
        return self::labels()[$value];
    }
}
