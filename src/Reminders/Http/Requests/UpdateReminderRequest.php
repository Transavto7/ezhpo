<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Src\Reminders\Enums\ReminderAction;

final class UpdateReminderRequest extends FormRequest
{
    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
            'action' => ['required', 'array'],
            'action.id' => ['required', 'string', 'in:'.implode(',', ReminderAction::cases())],
            'conditions' => ['required', 'array'],
            'conditions.*' => ['nullable', 'array'],'hidden_from_initiator' => ['nullable', 'boolean'],
            'users_to_notify' => ['nullable', 'array'],
            'users_to_notify.*' => ['nullable', 'integer'],
            'expires_at' => ['nullable', 'date_format:Y-m-d H:i'],
            'expires_in_minutes' => ['nullable', 'integer', 'min:0', 'max:43200']
        ];
    }
}
