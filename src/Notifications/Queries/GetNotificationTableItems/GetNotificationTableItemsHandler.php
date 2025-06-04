<?php

namespace Src\Notifications\Queries\GetNotificationTableItems;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Src\Core\ValueObjects\TableItems;

final class GetNotificationTableItemsHandler
{
    /**
     * @var GetNotificationTableItemsFiltersFactory
     */
    private $filtersFactory;

    /**
     * @param GetNotificationTableItemsFiltersFactory $filtersFactory
     */
    public function __construct(GetNotificationTableItemsFiltersFactory $filtersFactory)
    {
        $this->filtersFactory = $filtersFactory;
    }

    public function handle(GetNotificationTableItemsQuery $query): TableItems
    {
        $builder = DB::table('notifications')
            ->select([
                'notifications.*',
                'reminders.title as reminder_title',
                'initiator_user_employee.name as initiator_user_name',
                'initiator_user_employee.id as initiator_employee_id',
                'user_employee.name as user_name',
                'user_employee.id as employee_id',
            ])
            ->leftJoin('reminders', 'reminders.id', '=', 'notifications.reminder_id')
            ->leftJoin('employees as initiator_user_employee', 'initiator_user_employee.related_user_id', '=', 'notifications.initiator_user_id')
            ->leftJoin('employees as user_employee', 'user_employee.related_user_id', '=', 'notifications.user_id')
            ->when(! $query->isCanViewOther(), function ($q) use ($query) {
                $q->where('notifications.user_id', $query->getUserId());
            });

        $filtersPipe = $this->filtersFactory->createFiltersPipe($query->getFilters()->toArray());
        $builder = $filtersPipe->run($builder);

        if ($query->getSortBy()) {
            $dir = $query->isSortDesc() ? 'desc' : 'asc';
            $builder->orderBy($query->getSortBy(), $dir);
        }

        $paginator = $builder->paginate($query->getPerPage(), ['*'], 'page', $query->getPage());

        $items = $paginator->getCollection()
            ->map(function ($item) use ($query) {
                $isOwner = $item->user_id === $query->getUserId();

                $canMarkAsRead = $isOwner && $item->read_at === null;
                $canMarkAsCompleted = ($isOwner && $item->read_at !== null && $item->completed_at === null)
                    || (! $isOwner && $query->isCanChangeOther() && $item->read_at === null && $item->completed_at === null);

                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'content' => $item->content,
                    'reminder_url' => route('reminders.list-page', ['id' => $item->reminder_id, 'title' => $item->reminder_title]),
                    'reminder_title' => $item->reminder_title,
                    'user' => $item->user_name,
                    'user_url' => route('employees.index', ['employee_id' => [$item->employee_id]]),
                    'initiator_user' => $item->initiator_user_name,
                    'initiator_user_url' => route('employees.index', ['employee_id' => [$item->initiator_employee_id]]),
                    'is_expired' => $item->is_expired !== 0,
                    'expires_at' => $item->expires_at ? Carbon::parse($item->expires_at)->format('d.m.Y H:i') : null,
                    'read_at' => $item->read_at ? Carbon::parse($item->read_at)->format('d.m.Y H:i') : null,
                    'completed_at' => $item->completed_at ? Carbon::parse($item->completed_at)->format('d.m.Y H:i') : null,
                    'created_at' => Carbon::parse($item->created_at)->format('d.m.Y H:i'),
                    'can_mark_as_read' => $canMarkAsRead,
                    'can_mark_as_completed' => $canMarkAsCompleted,
                ];
            })
            ->toArray();

        return new TableItems(
            $items,
            $paginator->total(),
        );
    }
}
