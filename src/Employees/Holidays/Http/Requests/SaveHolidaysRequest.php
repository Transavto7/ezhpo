<?php
declare(strict_types=1);

namespace Src\Employees\Holidays\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SaveHolidaysRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deleted_days' => 'array',
            'deleted_days.*' => 'date_format:Y-m-d',
            'added_days' => 'array',
            'added_days.*' => 'date_format:Y-m-d',
        ];
    }
}
