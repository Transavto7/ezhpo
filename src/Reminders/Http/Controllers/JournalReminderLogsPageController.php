<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

use App\Enums\FeaturesEnum;
use Unleash\Client\Unleash;

final class JournalReminderLogsPageController
{
    public function __invoke(Unleash $unleash)
    {
        if (! $unleash->isEnabled(FeaturesEnum::REMINDERS_ENABLED)) {
            return view('common.disabled-feature-page', ['title' => 'Напоминания']);
        }

        $user = auth()->user();

        $canEmployeeRead = $user->access('employee_read');
        $canRemindersRead = $user->access('reminders_read');

        return view('Reminders::logs-list', [
            'canEmployeeRead' => $canEmployeeRead,
            'canRemindersRead' => $canRemindersRead,
        ]);
    }
}
