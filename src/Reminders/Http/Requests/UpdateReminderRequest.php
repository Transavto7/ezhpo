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
            'conditions.*' => ['nullable', 'array'],
        ];
    }
}
