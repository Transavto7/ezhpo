<?php

declare(strict_types=1);

namespace Src\Terminals\Verification\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class RetrySendCodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|string',
        ];
    }
}
