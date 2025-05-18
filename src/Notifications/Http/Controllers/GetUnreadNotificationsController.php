<?php

namespace Src\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Src\Notifications\Queries\GetUnreadUserNotifications\GetUnreadUserNotificationsHandler;
use Src\Notifications\Queries\GetUnreadUserNotifications\GetUnreadUserNotificationsQuery;

final class GetUnreadNotificationsController extends Controller
{
    public function __invoke(GetUnreadUserNotificationsHandler $handler): JsonResponse
    {
        $notifications = $handler->handle(new GetUnreadUserNotificationsQuery(Auth::id()));

        return response()->json($notifications);
    }
}
