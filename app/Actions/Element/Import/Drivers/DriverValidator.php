<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Drivers;

use App\Actions\Element\Import\Core\ElementValidator;
use App\Rules\DateIsCorrectFormat;
use App\Rules\DateIsCorrectFormatOrNull;
use Illuminate\Support\Facades\Validator as IlluminateValidator;

final class DriverValidator extends ElementValidator
{
    public function validate(array $parsedDataItem)
    {
        $this->errors = [];
        $attributes = [
            'companyInn' => 'ИНН компании',
            'fullName' => 'ФИО',
            'birthday' => 'Дата рождения',
            'companyName' => 'Название компании',
            'gender' => 'Пол',
            'phone' => 'Телефон',
            'snils' => 'СНИЛС',
            'license' => 'Серия/номер ВУ',
            'licenseIssuedAt' => 'Срок действия ВУ',
        ];

        $validator = IlluminateValidator::make($parsedDataItem, [
            'companyInn' => ['required', 'regex:/^\d{1,12}$/'],
            'fullName' => ['required', 'string', 'max:255'],
            'birthday' => [
                'required',
                'string',
                'max:255',
                new DateIsCorrectFormat(),
            ],
            'companyName' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'regex:/^(муж|жен|мужской|женский|м|ж)$/iu'],
            'phone' => ['nullable', 'string', 'max:255'],
            'snils' => ['nullable', 'string', 'max:255'],
            'license' => ['nullable', 'string', 'max:255'],
            'licenseIssuedAt' => ['nullable', 'string', 'max:255', new DateIsCorrectFormatOrNull()],
        ], [
            'gender.regex' => 'Пол должен быть одним из вариантов: муж,жен,мужской,женский,м,ж',
            'companyInn.regex' => 'ИНН компании должен быть до 12 цифр'
        ], $attributes);

        if ($validator->fails()) {
            $this->errors = $validator->errors()->all();
        }
    }
}
