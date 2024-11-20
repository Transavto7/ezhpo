<?php

namespace App\Enums;

use App\Models\Forms\BddForm;
use App\Models\Forms\MedicForm;
use App\Models\Forms\PrintPlForm;
use App\Models\Forms\ReportCartForm;
use App\Models\Forms\TechForm;

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

    public static function labelByType(string $type): string
    {
        $labels = [
            'tech' => 'Технический осмотр',
            'medic' => 'Медицинский осмотр',
            'bdd' => 'Инструктаж по БДД',
            'pechat_pl' => 'Печать путевых листов',
            'report_cart' => 'Снятие отчета с карт',
        ];

        return array_key_exists($type, $labels)
            ? $labels[$type]
            : $type;
    }

    public static function label(string $value = null): string
    {
        if (empty($value)) {
            return '';
        }

        return self::labels()[$value] ?? $value;
    }
}
