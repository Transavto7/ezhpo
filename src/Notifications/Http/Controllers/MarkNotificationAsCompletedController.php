<?php

declare(strict_types=1);

namespace Src\Notifications\Http\Controllers;

use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Src\Notifications\Commands\MarkNotificationAsCompleted\MarkNotificationAsCompletedCommand;
use Src\Notifications\Commands\MarkNotificationAsCompleted\MarkNotificationAsCompletedHandler;

final class MarkNotificationAsCompletedController
{
    public function __invoke(string $notificationId, MarkNotificationAsCompletedHandler $handler): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $handler->handle(new MarkNotificationAsCompletedCommand($notificationId, $user->id));

        return response()->noContent();
    }
}
