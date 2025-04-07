<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Src\Reminders\Commands\CreateReminderLog\ShowReminderLogCommand;
use Src\Reminders\Commands\CreateReminderLog\ShowReminderLogHandler;

final class ShowReminderLogController
{
    public function __invoke(string $reminderId, ShowReminderLogHandler $handler): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $handler->handle(new ShowReminderLogCommand($reminderId, $user->id));

        return response()->noContent();
    }
}
