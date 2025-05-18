<?php

declare(strict_types=1);

namespace Src\Notifications\Http\Controllers;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Src\Notifications\Commands\CreateNotificationsByContext\CreateNotificationsByContextCommand;
use Src\Notifications\Commands\CreateNotificationsByContext\CreateNotificationsByContextHandler;
use Src\Notifications\Repositories\NotificationRepository;
use Src\Reminders\Enums\ReminderAction;
use Symfony\Component\HttpFoundation\Response;

final class CreateNotificationsByContextController
{
    public function __invoke(
        Request $request,
        CreateNotificationsByContextHandler $handler,
        NotificationRepository $notificationRepository
    ): JsonResponse {
        /** @var User $user */
        $user = Auth::user();

        $action = ReminderAction::from($request->input('action'));

        $handler->handle(new CreateNotificationsByContextCommand($action, $request->input('context', []), $user));

        return response()->json()->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}
