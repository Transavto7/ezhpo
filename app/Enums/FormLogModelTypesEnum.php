<?php

namespace App\Enums;

use App\Car;
use App\Company;
use App\Driver;
use App\Models\Contract;
use App\Models\Forms\BddForm;
use App\Models\Forms\Form;
use App\Models\Forms\MedicForm;
use App\Models\Forms\PrintPlForm;
use App\Models\Forms\ReportCartForm;
use App\Models\Forms\TechForm;
use App\Product;
use App\Stamp;
use App\User;

class FormLogModelTypesEnum
{
    public static function fieldPromptsTypeMap(): array
    {
        return [
            TechForm::class => 'tech',
            MedicForm::class => 'medic',
            BddForm::class => 'bdd',
            PrintPlForm::class => 'pechat_pl',
            ReportCartForm::class => 'report_cart',
        ];
    }

    public static function labels(): array
    {
        return [
            TechForm::class => 'Технический осмотр',
            MedicForm::class => 'Медицинский осмотр',
            BddForm::class => 'Инструктаж по БДД',
            PrintPlForm::class => 'Печать путевых листов',
            ReportCartForm::class => 'Снятие отчета с карт',
        ];
    }

    public static function label(string $value = null): string
    {
        if (empty($value)) {
            return '';
        }

        return self::labels()[$value] ?? $value;
    }
}
