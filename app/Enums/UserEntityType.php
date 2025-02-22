<?php

namespace App\Enums;

final class UserEntityType extends Enum
{
    const COMPANY = 'company';
    const DRIVER = 'driver';
    const TERMINAL = 'terminal';
    const EMPLOYEE = 'employee';

    public static function company(): UserEntityType
    {
        return new self(self::COMPANY);
    }

    public static function driver(): UserEntityType
    {
        return new self(self::DRIVER);
    }

    public static function terminal(): UserEntityType
    {
        return new self(self::TERMINAL);
    }

    public static function employee(): UserEntityType
    {
        return new self(self::EMPLOYEE);
    }

    /**
     * @param string $value
     * @return UserEntityType
     */
    public static function from(string $value): Enum
    {
        switch (true) {
            case $value === self::COMPANY:
                return self::company();
            case $value === self::DRIVER:
                return self::driver();
            case $value === self::TERMINAL:
                return self::terminal();
            case $value === self::EMPLOYEE:
                return self::employee();
            default:
                throw new \LogicException("Unsupported user entity type value '$value'");
        }
    }

    public function getLabel(): string
    {
        switch (true) {
            case $this->value === self::COMPANY:
                return 'Компания';
            case $this->value === self::DRIVER:
                return 'Водитель';
            case $this->value === self::TERMINAL:
                return 'Терминал';
            case $this->value === self::EMPLOYEE:
                return 'Сотрудник';
            default:
                throw new \LogicException("Unsupported user entity type value '$value'");
        }
    }
}
