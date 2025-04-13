<?php

declare(strict_types=1);

namespace Src\Notifications\Http\Controllers;

use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Src\Notifications\Commands\ShowNotification\ShowNotificationCommand;
use Src\Notifications\Commands\ShowNotification\ShowNotificationHandler;

final class ShowNotificationController
{
    public function __invoke(string $reminderId, ShowNotificationHandler $handler): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $handler->handle(new ShowNotificationCommand($reminderId, $user->id));

        return response()->noContent();
    }
}
