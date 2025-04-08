<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

final class JournalReminderLogsPageController
{
    public function __invoke()
    {
        return view('reminders::log');
    }
}
