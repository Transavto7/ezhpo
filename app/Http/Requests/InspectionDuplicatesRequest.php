<?php

namespace App\Http\Requests;

use App\Enums\FormTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InspectionDuplicatesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'driverId' => 'required|string',
            'date' => 'required|date',
            'type' => 'required|string',
            'formType' => [
                'required',
                Rule::in(FormTypeEnum::toArray()),
            ]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
