<?php

namespace App\Enums\TripTicket;

class TransportationTypeEnum
{
    const REGULAR = 'regular';

    const ORDER = 'order';

    const TAXI = 'taxi';

    const CARGO = 'cargo';

    const SELF_NEEDS = 'self_needs';

    const CHILD_TRANSPORTATION = 'child_transportation';

    const SPECIAL_VEHICLE = 'special_vehicle';

    const CONTRACT = 'contract';

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

    public static function specialVehicle(): self
    {
        return new self(self::SPECIAL_VEHICLE);
    }

    public static function contract(): self
    {
        return new self(self::CONTRACT);
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
            case self::SPECIAL_VEHICLE:
                return self::specialVehicle();
            case self::CONTRACT:
                return self::contract();
            default:
                throw new \DomainException('Unknown transportation type: ' . $value);
        }
    }

    public static function labels(): array
    {
        return [
            self::CARGO => 'Перевозка грузов',
            self::REGULAR => 'Регулярная перевозка пассажиров и багажа',
            self::ORDER => 'Перевозка пассажиров и багажа по заказу',
            self::TAXI => 'Перевозка пассажиров и багажа легковым такси',
            self::CHILD_TRANSPORTATION => 'Организованная перевозка группы детей',
            self::SELF_NEEDS => 'Перевозка для собственных нужд',
            self::SPECIAL_VEHICLE => 'Передвижение и работа специальных транспортных средств',
            self::CONTRACT => 'Перевозка грузов на основании договора перевозки грузов или договора фрахтования (в т.ч. по договору аренды ТС с экипажем)',
        ];
    }

    public static function getLabel(string $value): string
    {
        return self::labels()[$value];
    }

    public static function forTemplate4C(): array
    {
        return [
            self::SELF_NEEDS,
            self::SPECIAL_VEHICLE,
            self::CONTRACT,
        ];
    }

    public static function forTemplate4P(): array
    {
        return [
            self::SELF_NEEDS,
            self::SPECIAL_VEHICLE,
            self::CONTRACT,
        ];
    }

    public static function forTemplatePG1(): array
    {
        return [
            self::SELF_NEEDS,
            self::SPECIAL_VEHICLE,
            self::CONTRACT,
        ];
    }

    public static function forTemplate6C(): array
    {
        return [
            self::ORDER,
            self::CHILD_TRANSPORTATION,
            self::SELF_NEEDS,
        ];
    }

    public static function forTemplate3C(): array
    {
        return [
            self::REGULAR,
            self::ORDER,
            self::SELF_NEEDS,
            self::SPECIAL_VEHICLE,
            self::CONTRACT,
        ];
    }

    public static function forTemplate4O(): array
    {
        return [
            self::SELF_NEEDS,
            self::SPECIAL_VEHICLE,
            self::CONTRACT,
        ];
    }

    public static function forTemplateECM2(): array
    {
        return [
            self::SELF_NEEDS,
            self::CONTRACT,
        ];
    }

    public static function forTemplate3(): array
    {
        return [
            self::REGULAR,
            self::ORDER,
            self::TAXI,
            self::SELF_NEEDS,
            self::CONTRACT,
        ];
    }
}
