<?php

declare(strict_types=1);

namespace Src\Notifications\Http\Controllers;

use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Src\Notifications\Commands\ExecuteNotification\ExecuteNotificationCommand;
use Src\Notifications\Commands\ExecuteNotification\ExecuteNotificationHandler;

final class ExecuteNotificationController
{
    public function __invoke(string $notificationId, ExecuteNotificationHandler $handler): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $handler->handle(new ExecuteNotificationCommand($notificationId, $user->id));

        return response()->noContent();
    }
}
