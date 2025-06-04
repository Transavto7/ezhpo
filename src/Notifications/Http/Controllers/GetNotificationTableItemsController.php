<?php

namespace Src\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Src\Notifications\Enums\NotificationFilterStatus;
use Src\Notifications\Queries\GetNotificationTableItems\GetNotificationTableItemsFilters;
use Src\Notifications\Queries\GetNotificationTableItems\GetNotificationTableItemsHandler;
use Src\Notifications\Queries\GetNotificationTableItems\GetNotificationTableItemsQuery;

final class GetNotificationTableItemsController extends Controller
{
    public function __invoke(Request $request, GetNotificationTableItemsHandler $handler)
    {
        $user = Auth::user();

        $canViewOther = $user->access('notifications_view_other');
        $canChangeOther = $user->access('notifications_change_other');

        $expiresAtBegin = null;
        if ($request->input('filters.expiresAtBegin')) {
            $expiresAtBegin = Carbon::parse($request->input('filters.expiresAtBegin'));
        }

        $expiresAtEnd = null;
        if ($request->input('filters.expiresAtEnd')) {
            $expiresAtEnd = Carbon::parse($request->input('filters.expiresAtEnd'));
        }

        $readAtBegin = null;
        if ($request->input('filters.readAtBegin')) {
            $readAtBegin = Carbon::parse($request->input('filters.readAtBegin'));
        }

        $readAtEnd = null;
        if ($request->input('filters.readAtEnd')) {
            $readAtEnd = Carbon::parse($request->input('filters.readAtEnd'));
        }

        $completedAtBegin = null;
        if ($request->input('filters.completedAtBegin')) {
            $completedAtBegin = Carbon::parse($request->input('filters.completedAtBegin'));
        }

        $completedAtEnd = null;
        if ($request->input('filters.completedAtEnd')) {
            $completedAtEnd = Carbon::parse($request->input('filters.completedAtEnd'));
        }

        $createdAtBegin = null;
        if ($request->input('filters.createdAtBegin')) {
            $createdAtBegin = Carbon::parse($request->input('filters.createdAtBegin'));
        }

        $createdAtEnd = null;
        if ($request->input('filters.createdAtEnd')) {
            $createdAtEnd = Carbon::parse($request->input('filters.createdAtEnd'));
        }

        $status = $request->input('filters.status');
        if ($status) {
            $status = NotificationFilterStatus::tryFrom($status);
        }


        $items = $handler->handle(new GetNotificationTableItemsQuery(
            $user->id,
            $canViewOther,
            $canChangeOther,
            $request->input('page'),
            $request->input('perPage'),
            $request->input('sortBy'),
            $request->input('sortDesc'),
            new GetNotificationTableItemsFilters(
                $request->input('filters.notifications') ?? [],
                $request->input('filters.search') ?? '',
                $request->input('filters.users') ?? [],
                $request->input('filters.initiatorUsers') ?? [],
                $request->input('filters.reminders') ?? [],
                $expiresAtBegin,
                $expiresAtEnd,
                $readAtBegin,
                $readAtEnd,
                $completedAtBegin,
                $completedAtEnd,
                $createdAtBegin,
                $createdAtEnd,
                $status
            )
        ));

        return response()->json($items);
    }
}
