<?php

namespace App\Enums\TripTicket;

final class TripTicketType
{
    const GENERATED = 'generated';
    const IN_ADVANCE = 'in_advance';
    const COMMON = 'common';

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

    public static function generated(): self
    {
        return new self(self::GENERATED);
    }

    public static function inAdvance(): self
    {
        return new self(self::IN_ADVANCE);
    }

    public static function common(): self
    {
        return new self(self::COMMON);
    }

    public static function fromString(string $value): self
    {
        switch ($value) {
            case self::GENERATED:
                return self::generated();
            case self::IN_ADVANCE:
                return self::inAdvance();
            case self::COMMON:
                return self::common();
            default:
                throw new \DomainException('Unknown trip ticket type: ' . $value);
        }
    }

    public static function labels(): array
    {
        return [
            self::GENERATED => 'Сгенерирован на основе осмотров',
            self::IN_ADVANCE => 'Создан наперед',
            self::COMMON => 'Текущий учет',
        ];
    }

    public static function getLabel(string $value): string
    {
        return self::labels()[$value];
    }
}
