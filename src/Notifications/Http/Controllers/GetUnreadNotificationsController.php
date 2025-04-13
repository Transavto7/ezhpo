<?php

namespace Src\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Src\Notifications\Queries\GetUnreadUserNotifications\GetUnreadUserNotificationsHandler;
use Src\Notifications\Queries\GetUnreadUserNotifications\GetUnreadUserNotificationsQuery;
use Src\Notifications\Queries\NotificationViewModel;

final class GetUnreadNotificationsController extends Controller
{
    public function __invoke(GetUnreadUserNotificationsHandler $handler): JsonResponse
    {
        $notifications = $handler->handle(new GetUnreadUserNotificationsQuery(Auth::id()));

        return response()->json(array_map(function (NotificationViewModel $notificationViewModel) {
            return $notificationViewModel->toArray();
        }, $notifications));
    }
}
