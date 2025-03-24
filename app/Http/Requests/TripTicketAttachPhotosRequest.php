<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TripTicketAttachPhotosRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'photos' => 'required|array',
            'photos.*' => 'file|mimes:jpeg,jpg,png,pdf,webp|max:8192',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Файл обязателен для загрузки.',
            'file.file' => 'Загруженный файл недействителен.',
            'file.mimes' => 'Файл должен быть в формате JPEG, JPG, PNG или PDF.',
            'file.max' => 'Размер файла не должен превышать 8 МБ.',
        ];
    }
}
