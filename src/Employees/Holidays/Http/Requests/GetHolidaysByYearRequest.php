<?php
declare(strict_types=1);

namespace Src\Employees\Holidays\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class GetHolidaysByYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'year' => 'numeric|digits:4',
        ];
    }
}
