<?php

declare(strict_types=1);

namespace Src\Notifications\Http\Controllers;

use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Src\Notifications\Commands\MarkNotificationAsRead\MarkNotificationAsReadCommand;
use Src\Notifications\Commands\MarkNotificationAsRead\MarkNotificationAsReadHandler;

final class MarkNotificationAsReadController
{
    public function __invoke(string $id, MarkNotificationAsReadHandler $handler): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $handler->handle(new MarkNotificationAsReadCommand($id, $user->id));

        return response()->noContent();
    }
}
