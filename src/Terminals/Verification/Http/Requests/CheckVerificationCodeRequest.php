<?php

declare(strict_types=1);

namespace Src\Terminals\Verification\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CheckVerificationCodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => 'required|string',
            'id' => 'required|string',
        ];
    }
}
