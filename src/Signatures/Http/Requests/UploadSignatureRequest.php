<?php
declare(strict_types=1);

namespace Src\Signatures\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UploadSignatureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'signature' => ['required', 'file'],
        ];
    }
}
