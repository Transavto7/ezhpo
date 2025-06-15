<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Enums\ReminderStatus;
use Src\Reminders\Enums\ReminderType;

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
            'type' => ['required', 'array'],
            'type.id' => ['required', 'string', 'in:'.implode(',', ReminderType::cases())],
            'status' => ['required', 'array'],
            'status.id' => ['required', 'string', 'in:'.implode(',', ReminderStatus::cases())],
            'conditions' => ['required', 'array'],
            'conditions.*' => ['nullable', 'array'], 'hidden_from_initiator' => ['nullable', 'boolean'],
            'usersToNotify' => ['nullable', 'array'],
            'usersToNotify.*' => ['nullable'],
            'expiresAt' => ['nullable', 'date_format:Y-m-d H:i'],
            'expiresInMinutes' => ['nullable', 'integer', 'min:0', 'max:43200'],
            'hiddenFromInitiator' => ['required', 'boolean'],
            'oneTimePerUser' => ['required', 'boolean'],
            'untilAnyUserCompletes' => ['required', 'boolean'],
        ];
    }
}
