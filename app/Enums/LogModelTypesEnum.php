<?php

namespace App\Enums;

use App\Car;
use App\Company;
use App\Driver;
use App\Models\Contract;
use App\Models\Service;
use App\Product;

class LogModelTypesEnum
{
    public static function fieldPromptsTypeMap(): array
    {
        return [
            Driver::class => 'driver',
            Company::class => 'company',
            Car::class => 'car',
            Contract::class => 'contracts',
            Service::class => 'product',
            Product::class => 'product',
        ];
    }

    public static function labels(): array
    {
        return [
            Driver::class => 'Водители',
            Company::class => 'Компании',
            Car::class => 'ТС',
            Contract::class => 'Договора',
            Service::class => 'Услуги',
            Product::class => 'Услуги'
        ];
    }

    public static function label(string $value): string
    {
        return self::labels()[$value] ?? $value;
    }
}
