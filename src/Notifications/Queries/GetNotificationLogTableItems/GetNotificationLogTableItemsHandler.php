<?php

namespace Src\Notifications\Queries\GetNotificationLogTableItems;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Src\Core\ValueObjects\TableItems;
use Src\Notifications\Enums\NotificationLogAction;

final class GetNotificationLogTableItemsHandler
{
    public function handle(GetNotificationLogTableItemsQuery $query): TableItems
    {
        $builder = DB::table('notification_logs')
            ->select([
                'notification_logs.*',
                'notifications.title',
                'notifications.id as notification_id',
                'employees.id as user_id',
                'employees.name as user_name',
            ])
            ->leftJoin('notifications', 'notification_logs.notification_id', '=', 'notifications.id')
            ->leftJoin('employees', 'employees.related_user_id', '=', 'notification_logs.user_id')
            ->when($query->getFilters()->getSearch() !== '', function ($q) use ($query) {
                $q->where('notifications.title', 'like', '%'.$query->getFilters()->getSearch().'%');
            })
            ->when(count($query->getFilters()->getNotificationsIds()), function ($q) use ($query) {
                $q->whereIn('notifications.id', $query->getFilters()->getNotificationsIds());
            })
            ->when(count($query->getFilters()->getUserIds()), function ($q) use ($query) {
                $q->whereIn('notification_logs.user_id', $query->getFilters()->getUserIds());
            })
            ->when(count($query->getFilters()->getActions()), function ($q) use ($query) {
                $q->whereIn('notification_logs.action', $query->getFilters()->getActions());
            });

        if ($query->getSortBy()) {
            $dir = $query->isSortDesc() ? 'desc' : 'asc';
            $builder->orderBy($query->getSortBy(), $dir);
        }

        $paginator = $builder->paginate($query->getPerPage(), ['*'], 'page', $query->getPage());

        $items = $paginator->getCollection()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'notification_url' => route('notifications.list-page', ['id' => $item->notification_id, 'title' => $item->title]),
                    'user' => $item->user_name,
                    'user_url' => route('employees.index', ['employee_id' => [$item->user_id]]),
                    'action' => [
                        'value' => $item->action,
                        'name' => NotificationLogAction::from($item->action)->getTitle(),
                    ],
                    'title' => $item->title,
                    'created_at' => Carbon::parse($item->created_at)->format('d.m.Y H:i:s'),
                ];
            })
            ->toArray();

        return new TableItems(
            $items,
            $paginator->total(),
        );
    }
}
