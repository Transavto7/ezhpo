<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

final class JournalReminderLogsPageController
{
    public function __invoke()
    {
        $user = auth()->user();

        $canEmployeeRead = $user->access('employee_read');
        $canRemindersRead = $user->access('reminders_read');

        return view('Reminders::logs-list', [
            'canEmployeeRead' => $canEmployeeRead,
            'canRemindersRead' => $canRemindersRead,
        ]);
    }
}
