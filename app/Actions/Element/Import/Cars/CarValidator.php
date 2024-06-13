<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Cars;

use App\Actions\Element\Import\Core\ElementValidator;
use App\Rules\DateIsCorrectFormat;
use App\Rules\DateIsCorrectFormatOrNull;
use Illuminate\Support\Facades\Validator as IlluminateValidator;
use Illuminate\Validation\Rule;

final class CarValidator extends ElementValidator
{
    public function validate(array $parsedDataItem)
    {
        $this->errors = [];
        $attributes = [
            'companyName' => 'Название компании',
            'companyInn' => 'ИНН компании',
            'number' => 'Гос номер',
            'markAndModel' => 'Марка и модель',
            'category' => 'Категория ТС',
            'trailer' => 'Прицеп (если есть)',
            'dateTechView' => 'Дата ТО',
            'dateOsago' => 'Дата ОСАГО',
            'dateSkzi' => 'Срок действия СКЗИ',
        ];

        $validator = IlluminateValidator::make($parsedDataItem, [
            'companyName' => ['nullable', 'string', 'max:255'],
            'companyInn' => ['required', 'integer'],
            'number' => ['required', 'string', 'max:255'],
            'markAndModel' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255', Rule::in(config('elements.Car.fields.type_auto.values'))],
            'trailer' => ['nullable', 'string', 'max:255', Rule::in(config('elements.Car.fields.trailer.values'))],
            'dateTechView' => ['nullable', 'string', 'max:255', new DateIsCorrectFormatOrNull()],
            'dateOsago' => ['nullable', 'string', 'max:255', new DateIsCorrectFormatOrNull()],
            'dateSkzi' => ['nullable', 'string', 'max:255', new DateIsCorrectFormatOrNull()],

        ], [], $attributes);

        if ($validator->fails()) {
            $this->errors = $validator->errors()->all();
        }
    }
}
