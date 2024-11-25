<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateCompanyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'req_id' => 'required|string|max:191',
            'inn' => 'required|string|max:191',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
