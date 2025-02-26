<?php

namespace App\Enums;

final class TripTicketActionType
{
    const ATTACH_MEDIC_FORM = 'attach_medic_form';
    const DETACH_MEDIC_FORM = 'detach_medic_form';
    const ATTACH_TECH_FORM = 'attach_tech_form';
    const DETACH_TECH_FORM = 'detach_tech_form';
    const CHANGE_STATUS = 'change_status';
    const ACTIVATE = 'activate';
    const DEACTIVATE = 'deactivate';

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

    public static function attachMedicForm(): self
    {
        return new self(self::ATTACH_MEDIC_FORM);
    }

    public static function detachMedicForm(): self
    {
        return new self(self::DETACH_MEDIC_FORM);
    }

    public static function attachTechForm(): self
    {
        return new self(self::ATTACH_TECH_FORM);
    }

    public static function detachTechForm(): self
    {
        return new self(self::DETACH_TECH_FORM);
    }

    public static function changeStatus(): self
    {
        return new self(self::CHANGE_STATUS);
    }

    public static function activate(): self
    {
        return new self(self::ACTIVATE);
    }

    public static function deactivate(): self
    {
        return new self(self::DEACTIVATE);
    }

    public static function fromString(string $value): self
    {
        switch ($value) {
            case self::ATTACH_MEDIC_FORM:
                return self::attachMedicForm();
            case self::DETACH_MEDIC_FORM:
                return self::detachMedicForm();
            case self::ATTACH_TECH_FORM:
                return self::attachTechForm();
            case self::DETACH_TECH_FORM:
                return self::detachTechForm();
            case self::CHANGE_STATUS:
                return self::changeStatus();
            case self::ACTIVATE:
                return self::activate();
            case self::DEACTIVATE:
                return self::deactivate();
            default:
                throw new \DomainException('Unknown trip ticket action type: ' . $value);
        }
    }

    public static function labels(): array
    {
        return [
            self::ATTACH_MEDIC_FORM => 'Привязка медосмотра к путевому листу',
            self::DETACH_MEDIC_FORM => 'Отвязка медосмотра от путевого листа',
            self::ATTACH_TECH_FORM => 'Привязка техосмотра к путевому листу',
            self::DETACH_TECH_FORM => 'Отвязка техосмотра от путевого листа',
            self::CHANGE_STATUS => 'Изменение статуса путевого листа',
            self::ACTIVATE => 'Утверждение путевого листа',
            self::DEACTIVATE => 'Отмена утверждения путевого листа',
        ];
    }

    public static function getLabel(string $value): string
    {
        return self::labels()[$value];
    }
}
