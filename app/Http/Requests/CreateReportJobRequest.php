<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateReportJobRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date_to' => 'required|date',
            'date_from' => 'required|date',
            'company_id' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
