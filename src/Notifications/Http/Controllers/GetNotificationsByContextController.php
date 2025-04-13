<?php

declare(strict_types=1);

namespace Src\Notifications\Http\Controllers;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Src\Notifications\Entitites\Notification;
use Src\Notifications\Queries\NotificationViewModel;
use Src\Notifications\Repository\NotificationRepository;
use Src\Reminders\ConditionBuilder\ContextConditionBuilder;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Queries\GetRemindersByContext\GetNotificationsByContextHandler;
use Src\Reminders\Queries\GetRemindersByContext\GetNotificationsByContextQuery;

final class GetNotificationsByContextController
{
    public function __invoke(
        Request                          $request,
        ContextConditionBuilder          $conditionBuilder,
        GetNotificationsByContextHandler $handler,
        NotificationRepository           $notificationRepository
    ): JsonResponse {
        /** @var User $user */
        $user = Auth::user();

        /** @var int $id */
        $id = Auth::id();
        $conditions = $conditionBuilder->build(array_merge($request->input('context', []), ['user' => $id]));
        $action = ReminderAction::from($request->input('action'));

        $notifications = $handler->handle(new GetNotificationsByContextQuery($action, $conditions, $user));

        return response()->json(
            array_map(function (NotificationViewModel $notification) {
                return $notification->toArray();
            }, $notifications)
        );
    }
}
