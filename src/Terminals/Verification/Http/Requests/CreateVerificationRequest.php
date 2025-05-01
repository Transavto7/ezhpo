<?php

declare(strict_types=1);

namespace Src\Terminals\Verification\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateVerificationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'driver_id' => 'required|string',
        ];
    }
}
