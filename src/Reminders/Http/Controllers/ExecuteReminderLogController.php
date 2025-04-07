<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Src\Reminders\Commands\CreateReminderLog\ExecuteReminderLogCommand;
use Src\Reminders\Commands\CreateReminderLog\ExecuteReminderLogHandler;

final class ExecuteReminderLogController
{
    public function __invoke(string $reminderId, ExecuteReminderLogHandler $handler): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $handler->handle(new ExecuteReminderLogCommand($reminderId, $user->id));

        return response()->noContent();
    }
}
